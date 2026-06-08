<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Donasi;
use App\Models\Proyek;
use Illuminate\Http\Request;
use App\Services\Midtrans\CallbackService;

class PaymentCallbackController extends Controller
{
    public function receive()
    {
        $callback = new CallbackService;

        if ($callback->isSignatureKeyVerified()) {
            $notification = $callback->getNotification();
            $order = $callback->getOrder();

            if ($callback->isSuccess()) {
                \Illuminate\Support\Facades\DB::transaction(function () use ($order) {
                    $orderRecord = Order::find($order->id);
                    if ($orderRecord) {
                        $orderRecord->update([
                            'payment_status' => Order::STATUS_SUCCESS,
                        ]);

                        // Deduct balance if combination payment
                        if ($orderRecord->payment_method === 'kombinasi') {
                            $donatur = $orderRecord->user;
                            if ($donatur) {
                                $donatur->kurangiSaldo($orderRecord->amount_saldo, "Pembayaran kombinasi donasi proyek: {$orderRecord->proyek->judul}");
                            }
                        }

                        // Update Proyek and create successful Donasi record
                        $proyek = $orderRecord->proyek;
                        if ($proyek) {
                            Donasi::updateOrCreate(
                                [
                                    'id_proyek' => $proyek->id,
                                    'id_donatur' => $orderRecord->user_id,
                                    'created_at' => $orderRecord->created_at ?? now(),
                                ],
                                [
                                    'nominal' => $orderRecord->total_price,
                                    'status' => 'success',
                                ]
                            );

                            // Increment the project's collected funding
                            $proyek->increment('dana_terkumpul', $orderRecord->total_price);
                            $proyek->refresh();

                            // Transition project status: only change to eksekusi when target reached
                            if ($proyek->dana_terkumpul >= $proyek->target_dana) {
                                $proyek->update(['status' => 'eksekusi']);
                            }
                            // Otherwise, tetap aktif_funding (no need to update)
                        }
                    }
                });
            }

            if ($callback->isExpire()) {
                Order::where('id', $order->id)->update([
                    'payment_status' => Order::STATUS_EXPIRED,
                ]);
            }

            if ($callback->isCancelled()) {
                Order::where('id', $order->id)->update([
                    'payment_status' => Order::STATUS_CANCELLED,
                ]);
            }

            return response()
                ->json([
                    'success' => true,
                    'message' => 'Notifikasi berhasil diproses',
                ]);
        } else {
            return response()
                ->json([
                    'error' => true,
                    'message' => 'Signature key tidak terverifikasi',
                ], 403);
        }
    }
}