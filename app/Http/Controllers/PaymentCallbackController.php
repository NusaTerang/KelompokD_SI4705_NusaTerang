<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Midtrans\CallbackService;

class PaymentCallbackController extends Controller
{
    public function receive()
    {
        $callback = new CallbackService;

        if ($callback->isSignatureKeyVerified()) {
            $notification = $callback->getNotification();
            $order        = $callback->getOrder();

            if ($callback->isSuccess()) {
                Order::where('id', $order->id)->update([
                    'payment_status' => Order::STATUS_SUCCESS,
                ]);
                
                // Tambahkan dana ke proyek terkait
                if ($order->proyek_id) {
                    $proyekTarget = \App\Models\Proyek::find($order->proyek_id);
                    if ($proyekTarget) {
                        $proyekTarget->dana_terkumpul += $order->total_price;
                        $proyekTarget->save();
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

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi berhasil diproses',
            ]);
        } else {
            return response()->json([
                'error'   => true,
                'message' => 'Signature key tidak terverifikasi',
            ], 403);
        }
    }
}
