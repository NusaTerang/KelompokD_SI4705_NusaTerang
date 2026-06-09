<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Events\DonationSucceeded;
use App\Events\PaymentConfirmed;
use App\Services\FundingTrackingService;

class Donation extends Model
{
    protected $fillable = [
        'project_id',
        'user_id',
        'amount',
        'status',
    ];

    protected $casts = [
        'amount' => 'float',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id_donatur');
    }

    protected static function booted()
    {
        static::saved(function ($donation) {
            $statusChanged = $donation->wasChanged('status') || $donation->wasRecentlyCreated;

            if ($statusChanged && $donation->status === 'success') {
                event(new DonationSucceeded($donation));
                event(new PaymentConfirmed($donation));
            } elseif ($donation->wasChanged('status') && $donation->getOriginal('status') === 'pending') {
                app(FundingTrackingService::class)->updateFundingStatus($donation->project_id);
            }
        });
    }
}
