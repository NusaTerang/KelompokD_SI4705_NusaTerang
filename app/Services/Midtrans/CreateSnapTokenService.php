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
                    'id'       => 'DONASI-' . $this->order->id,
                    'price'    => (int) $this->order->total_price,
                    'quantity' => 1,
                    'name'     => 'Donasi NusaTerang',
                    'category' => 'Donation',
                ],
            ],
            'customer_details' => [
                'first_name' => $this->order->donatur_name,
                'email'      => $this->order->donatur_email,
                'phone'      => $this->order->donatur_phone ?? '',
            ],
            // Batasi ke QRIS saja agar sesuai requirement
            'enabled_payments' => ['qris'],
        ];

        return Snap::getSnapToken($params);
    }
}