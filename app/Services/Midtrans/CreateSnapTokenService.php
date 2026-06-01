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
<<<<<<< HEAD
    }

    public function getSnapToken()
    {
        $params = [
            'transaction_details' => [
                'order_id' => $this->order->number,
                'gross_amount' => (int) $this->order->total_price,
            ],
            'item_details' => [
                [
                    'id' => $this->order->proyek_id,
                    'price' => (int) $this->order->total_price,
                    'quantity' => 1,
                    'name' => substr('Donasi ' . $this->order->proyek->judul, 0, 50),
                ],
            ],
            'customer_details' => [
                'first_name' => $this->order->user->nama,
                'email' => $this->order->user->email,
                'phone' => $this->order->user->no_telepon ?? '081234567890',
            ],
            'enabled_payments' => [
                'other_qris', 'gopay', 'shopeepay'
            ]
        ];

        return Snap::getSnapToken($params);
    }
}
=======
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
>>>>>>> 9e75b2c7211ecd112192778de4861ab8d620a9fd
