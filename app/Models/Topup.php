<?php

namespace App\Models;

use App\Services\SaldoService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Topup extends Model
{
    const STATUS_PENDING   = 1;
    const STATUS_SUCCESS   = 2;
    const STATUS_EXPIRED   = 3;
    const STATUS_CANCELLED = 4;

    protected $fillable = [
        'number',
        'user_id',
        'amount',
        'payment_status',
        'snap_token',
    ];

    protected $casts = [
        'amount'         => 'float',
        'payment_status' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id_donatur');
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

    // ─── Accessor agar kompatibel dengan CreateSnapTokenService ────────────
    public function getTotalPriceAttribute(): int
    {
        return (int) $this->amount;
    }

    public function getDonaturNameAttribute(): string
    {
        return $this->user->nama ?? 'Donatur';
    }

    public function getDonaturEmailAttribute(): string
    {
        return $this->user->email ?? '';
    }

    public function getDonaturPhoneAttribute(): string
    {
        return $this->user->no_telepon ?? '';
    }

    public function getSnapItemNameAttribute(): string
    {
        return 'Top Up Saldo NusaTerang';
    }

    /**
     * Tandai top up lunas dan kreditkan ke saldo donatur. Idempoten.
     */
    public function confirmPaid(): void
    {
        if ($this->isSuccess()) {
            return;
        }

        $this->update(['payment_status' => self::STATUS_SUCCESS]);

        app(SaldoService::class)->credit(
            $this->user,
            (float) $this->amount,
            'topup',
            'Top up saldo via QRIS'
        );
    }
}
