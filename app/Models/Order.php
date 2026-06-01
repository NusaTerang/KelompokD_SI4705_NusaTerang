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

    // ── Status constants ───────────────────────────────────────────
    const STATUS_PENDING   = 1; // menunggu pembayaran
    const STATUS_SUCCESS   = 2; // sudah dibayar
    const STATUS_EXPIRED   = 3; // kadaluarsa
    const STATUS_CANCELLED = 4; // dibatalkan

    // ── Mass-assignable fields ─────────────────────────────────────
    protected $fillable = [
        'user_id',
        'number',
        'total_price',
        'donatur_name',
        'donatur_email',
        'donatur_phone',
        'pesan',
        'payment_status',
        'snap_token',
        'proyek_id',
    ];

    // ── Casts ──────────────────────────────────────────────────────
    protected $casts = [
        'total_price'    => 'integer',
        'payment_status' => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────────────
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id_donatur');
    }

    public function proyek(): BelongsTo
    {
        return $this->belongsTo(Proyek::class, 'proyek_id');
    }

    // ── Helpers ────────────────────────────────────────────────────
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
}
