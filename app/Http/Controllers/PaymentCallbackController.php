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
                Order::where('id', $order->id)->update([
                    'payment_status' => Order::STATUS_SUCCESS,
                ]);

                // Update Proyek and create successful Donasi record
                $proyek = $order->proyek;
                if ($proyek) {
                    Donasi::updateOrCreate(
                        [
                            'id_proyek' => $proyek->id,
                            'id_donatur' => $order->user_id,
                            'created_at' => $order->created_at ?? now(),
                        ],
                        [
                            'nominal' => $order->total_price,
                            'status' => 'success',
                        ]
                    );

                    // Increment the project's collected funding
                    $proyek->increment('dana_terkumpul', $order->total_price);
                    $proyek->refresh();

                    // Transition project status based on target achievement
                    if ($proyek->dana_terkumpul >= $proyek->target_dana) {
                        $proyek->update(['status' => 'target_tercapai']);
                    } else {
                        $proyek->update(['status' => 'funding']);
                    }
                }
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