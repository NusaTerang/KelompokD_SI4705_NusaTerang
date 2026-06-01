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
        'proyek_id',
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

    public function proyek(): BelongsTo
    {
        return $this->belongsTo(Proyek::class);
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
}
