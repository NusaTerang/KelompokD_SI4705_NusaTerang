<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donasi extends Model
{
    protected $table = 'donasi';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id_donatur',
        'id_proyek',
        'nominal',
        'status',
        'refund_status',
        'refund_amount',
        'refunded_at',
    ];

    protected $casts = [
        'nominal'       => 'float',
        'refund_amount' => 'float',
        'refunded_at'   => 'datetime',
    ];

    public const REFUND_NONE     = 'none';
    public const REFUND_REFUNDED = 'refunded';
    public const REFUND_IKHLAS   = 'ikhlas';

    public function isRefundable(): bool
    {
        return $this->status === 'success' && $this->refund_status === self::REFUND_NONE;
    }

    public function donatur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_donatur', 'id_donatur');
    }

    /**
     * Alias dari donatur(). Sebagian kode (mis. API funding) merujuk relasi
     * ini dengan nama "user".
     */
    public function user(): BelongsTo
    {
        return $this->donatur();
    }

    public function proyek(): BelongsTo
    {
        return $this->belongsTo(Proyek::class, 'id_proyek');
    }
}
