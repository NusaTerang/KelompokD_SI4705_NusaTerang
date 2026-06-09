<?php

namespace App\Services\Midtrans;

use Midtrans\Transaction;

class CheckStatusService extends Midtrans
{
    /**
     * Menanyakan status terkini sebuah transaksi langsung ke Midtrans.
     * Dipakai untuk "Refresh Status" agar pembayaran tetap dapat dikonfirmasi
     * di localhost (di mana webhook Midtrans tidak bisa menjangkau server).
     *
     * @return object Notification payload (transaction_status, fraud_status, dst.)
     */
    public function getStatus(string $orderNumber)
    {
        return Transaction::status($orderNumber);
    }
}
