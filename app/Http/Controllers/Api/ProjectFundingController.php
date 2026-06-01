<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Donation;
use App\Services\FundingTrackingService;
use Illuminate\Http\JsonResponse;

class ProjectFundingController extends Controller
{
    protected FundingTrackingService $trackingService;

    public function __construct(FundingTrackingService $trackingService)
    {
        $this->trackingService = $trackingService;
    }

    public function show($id): JsonResponse
    {
        $project = Project::findOrFail($id);

        $jumlahDonatur = $this->trackingService->calculateJumlahDonatur($project->id);
        $progress = $this->trackingService->calculateProgress($project->id);

        return response()->json([
            'project_id' => (int) $project->id,
            'target_dana' => (float) $project->target_dana,
            'dana_terkumpul' => (float) $project->dana_terkumpul,
            'progress_percentage' => (int) round($progress),
            'jumlah_donatur' => (int) $jumlahDonatur,
            'funding_status' => $project->funding_status,
        ]);
    }

    public function feed($id): JsonResponse
    {
        $project = Project::findOrFail($id);

        $donations = Donation::where('project_id', $project->id)
            ->where('status', 'success')
            ->with('user')
            ->latest()
            ->get();

        return response()->json($donations);
    }
}
