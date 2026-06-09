<?php

namespace App\Models;

use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        if ($this->isSuccess()) {
            return;
        }

        $this->update(['payment_status' => self::STATUS_SUCCESS]);

        $proyek = $this->proyek;
        if ($proyek) {
            Donasi::create([
                'id_proyek'  => $proyek->id,
                'id_donatur' => $this->user_id,
                'nominal'    => $this->total_price,
                'status'     => 'success',
            ]);

            // Menambah dana_terkumpul, mengirim notifikasi TargetDanaTercapai saat
            // target terlampaui, dan (via ProyekObserver) memindah status proyek
            // aktif_funding -> eksekusi.
            $proyek->recordFunding($this->total_price);
        }
    }
}
