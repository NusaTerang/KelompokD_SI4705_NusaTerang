<?php

namespace App\Services\Midtrans;

use Midtrans\Snap;

class CreateSnapTokenService extends Midtrans
{
    protected $order;

    public function __construct($order)
    {
        parent::__construct();
        $this->order = $order;
    }

    public function getSnapToken(): string
    {
        $params = [
            'transaction_details' => [
                'order_id'     => $this->order->number,
                'gross_amount' => (int) $this->order->total_price,
            ],
            'item_details' => [
                [
                    'id'       => 'TRX-' . $this->order->id,
                    'price'    => (int) $this->order->total_price,
                    'quantity' => 1,
                    'name'     => $this->order->snap_item_name ?? 'Donasi NusaTerang',
                    'category' => 'NusaTerang',
                ],
            ],
            'customer_details' => [
                'first_name' => $this->order->donatur_name,
                'email'      => $this->order->donatur_email,
                'phone'      => $this->order->donatur_phone ?? '',
            ],
            // Batasi ke QRIS saja agar sesuai requirement.
            // Channel "Other QRIS" di dashboard Midtrans = kode 'other_qris'.
            // 'qris' (QRIS-via-GoPay) disertakan sebagai cadangan; hanya channel
            // yang aktif di dashboard yang akan tampil di Snap.
            'enabled_payments' => ['other_qris', 'qris'],
        ];

        return Snap::getSnapToken($params);
    }
}
