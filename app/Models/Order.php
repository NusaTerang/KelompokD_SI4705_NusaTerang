<?php

namespace App\Models;

use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    const STATUS_PENDING   = 1;
    const STATUS_SUCCESS   = 2;
    const STATUS_EXPIRED   = 3;
    const STATUS_CANCELLED = 4;

    protected $fillable = [
        'user_id',
        'proyek_id',
        'number',
        'total_price',
        'donatur_name',
        'donatur_email',
        'donatur_phone',
        'pesan',
        'payment_status',
        'snap_token',
        'payment_method',
        'amount_saldo',
        'amount_qris',
    ];

    protected $casts = [
        'total_price'    => 'integer',
        'payment_status' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id_donatur');
    }

    public function proyek(): BelongsTo
    {
        return $this->belongsTo(Proyek::class, 'proyek_id');
    }

    public function isPending(): bool
    {
        return $this->payment_status === self::STATUS_PENDING;
    }

    public function isSuccess(): bool
    {
        return $this->payment_status === self::STATUS_SUCCESS;
    }

    public function isExpired(): bool
    {
        return $this->payment_status === self::STATUS_EXPIRED;
    }

    public function isCancelled(): bool
    {
        return $this->payment_status === self::STATUS_CANCELLED;
    }

    /**
     * Tandai order sebagai lunas dan catat donasinya. Idempoten: aman dipanggil
     * berkali-kali (webhook Midtrans maupun pull "Refresh Status") — transisi
     * dana hanya terjadi sekali.
     */
    public function confirmPaid(): void
    {
        DB::transaction(function () {
            $order = self::query()
                ->whereKey($this->getKey())
                ->lockForUpdate()
                ->first();

            if (! $order || $order->isSuccess()) {
                return;
            }

            $proyek = $order->proyek;
            if (! $proyek) {
                return;
            }

            $donatur = $order->user;
            if ($donatur && $order->payment_method === 'saldo') {
                $donatur->kurangiSaldo(
                    (float) ($order->amount_saldo ?: $order->total_price),
                    "Donasi saldo untuk proyek: {$proyek->judul}"
                );
            } elseif ($donatur && $order->payment_method === 'kombinasi' && $order->amount_saldo > 0) {
                $donatur->kurangiSaldo(
                    (float) $order->amount_saldo,
                    "Pembayaran kombinasi donasi proyek: {$proyek->judul}"
                );
            }

            $order->update(['payment_status' => self::STATUS_SUCCESS]);

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

            // Menambah dana_terkumpul dan mengirim notifikasi TargetDanaTercapai
            // saat target terlampaui.
            $proyek->recordFunding($order->total_price);
        });

        $this->refresh();
    }
}
