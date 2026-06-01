<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Donation;
use App\Events\FundingTargetReached;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FundingTrackingService
{
    public function calculateDanaTerkumpul(int $projectId): float
    {
        return (float) Donation::where('project_id', $projectId)
            ->where('status', 'success')
            ->sum('amount');
    }

    public function calculateProgress(int $projectId): float
    {
        $project = Project::findOrFail($projectId);
        if ($project->target_dana <= 0) {
            return 0.0;
        }
        return min(($project->dana_terkumpul / $project->target_dana) * 100, 100.0);
    }

    public function calculateJumlahDonatur(int $projectId): int
    {
        return Donation::where('project_id', $projectId)
            ->where('status', 'success')
            ->distinct('user_id')
            ->count('user_id');
    }

    public function updateFundingStatus(int $projectId): void
    {
        DB::transaction(function () use ($projectId) {
            // Row locking to prevent race conditions
            $project = Project::lockForUpdate()->findOrFail($projectId);

            $oldDanaTerkumpul = (float) $project->dana_terkumpul;
            $oldStatus = $project->funding_status;

            // Recalculate using aggregate query
            $actualDanaTerkumpul = $this->calculateDanaTerkumpul($projectId);

            // Log data inconsistency if stored value differs from actual SUM
            if ($oldDanaTerkumpul !== $actualDanaTerkumpul) {
                Log::warning("Data inconsistency detected for Project #{$projectId}: stored dana_terkumpul is {$oldDanaTerkumpul}, but actual sum is {$actualDanaTerkumpul}.");
            }

            // Update dana_terkumpul
            $project->dana_terkumpul = $actualDanaTerkumpul;

            // Funding validation rules
            $targetStatus = 'funding';
            if ($actualDanaTerkumpul >= (float) $project->target_dana) {
                // Check for pending transactions
                $hasPending = Donation::where('project_id', $projectId)
                    ->where('status', 'pending')
                    ->exists();

                if ($hasPending) {
                    Log::info("Project #{$projectId} reached target funding, but has pending donations. Status remains 'funding'. Waiting for pending donations to resolve.");
                    $targetStatus = 'funding';
                } else {
                    $targetStatus = 'target_tercapai';
                }
            }

            $project->funding_status = $targetStatus;
            $project->save();

            // Log changes
            if ($oldDanaTerkumpul !== $actualDanaTerkumpul) {
                Log::info("Project #{$projectId} dana_terkumpul changed from {$oldDanaTerkumpul} to {$actualDanaTerkumpul}.");
            }

            if ($oldStatus !== $targetStatus) {
                Log::info("Project #{$projectId} funding_status changed from {$oldStatus} to {$targetStatus}.");

                // Dispatch FundingTargetReached once when transitioned from funding to target_tercapai
                if ($oldStatus === 'funding' && $targetStatus === 'target_tercapai') {
                    event(new FundingTargetReached($project));
                }
            }
        });
    }
}
