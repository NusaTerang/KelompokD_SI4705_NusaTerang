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
            $order = $callback->getOrder();

            if (! $order) {
                return response()->json([
                    'error' => true,
                    'message' => 'Order tidak ditemukan',
                ], 404);
            }

            if ($callback->isSuccess()) {
                $order->confirmPaid();
            }

            if ($callback->isExpire()) {
                $order->update(['payment_status' => Order::STATUS_EXPIRED]);
            }

            if ($callback->isCancelled()) {
                $order->update(['payment_status' => Order::STATUS_CANCELLED]);
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
