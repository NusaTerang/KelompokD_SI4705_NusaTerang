<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Desa;
use App\Services\PenyediaRecommendationService;

class PenyediaController extends Controller
{
    public function getRekomendasi(Request $request, PenyediaRecommendationService $service)
    {
        $desa_id = $request->query('desa_id');
        
        if (!$desa_id) {
            return response()->json(['error' => 'desa_id is required'], 400);
        }

        $desa = Desa::findOrFail($desa_id);
        
        $recommendations = $service->getRecommendations($desa);
        
        return response()->json([
            'status' => 'success',
            'data' => $recommendations
        ]);
    }
}
