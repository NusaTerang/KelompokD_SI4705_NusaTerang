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
        $price = $this->order->payment_method === 'kombinasi'
            ? (int) $this->order->amount_qris
            : (int) $this->order->total_price;

        $params = [
            'transaction_details' => [
                'order_id'     => $this->order->number,
                'gross_amount' => $price,
            ],
            'item_details' => [
                [
                    'id'       => 'DONASI-' . $this->order->id,
                    'price'    => $price,
                    'quantity' => 1,
                    'name'     => $this->order->payment_method === 'kombinasi' ? 'Kekurangan Donasi NusaTerang' : 'Donasi NusaTerang',
                    'category' => 'Donation',
                ],
            ],
            'customer_details' => [
                'first_name' => $this->order->donatur_name,
                'email'      => $this->order->donatur_email,
                'phone'      => $this->order->donatur_phone ?? '',
            ],
            // Tampilkan semua payment method yang tersedia (remove specific channel restrictions)
        ];

        return Snap::getSnapToken($params);
    }
}
