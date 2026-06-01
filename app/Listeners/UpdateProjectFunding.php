<?php

namespace App\Listeners;

use App\Services\FundingTrackingService;
use Illuminate\Support\Facades\Log;

class UpdateProjectFunding
{
    protected FundingTrackingService $trackingService;

    public function __construct(FundingTrackingService $trackingService)
    {
        $this->trackingService = $trackingService;
    }

    public function handle(object $event): void
    {
        if (isset($event->donation)) {
            $donation = $event->donation;
            Log::info("Event " . get_class($event) . " received for Donation #{$donation->id}. Updating project #{$donation->project_id} funding status.");
            $this->trackingService->updateFundingStatus($donation->project_id);
        }
    }
}
