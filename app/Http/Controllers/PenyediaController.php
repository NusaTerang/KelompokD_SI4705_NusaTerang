<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
<<<<<<< HEAD
use App\Models\Desa;
use App\Services\PenyediaRecommendationService;

class PenyediaController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\PenyediaEnergi::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('provinsi_operasi', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && in_array($request->status, ['aktif', 'nonaktif'])) {
            $query->where('status', $request->status);
        }

        $providers = $query->paginate(6)->withQueryString();
        return view('penyedia.index', compact('providers'));
    }

    public function show($id)
    {
        $provider = \App\Models\PenyediaEnergi::findOrFail($id);
        return view('penyedia.show', compact('provider'));
    }

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
=======

/** Stub — akan diimplementasi oleh tim */
class PenyediaController extends Controller
{
    public function index() { abort(501, 'Belum diimplementasi'); }
    public function show($id) { abort(501, 'Belum diimplementasi'); }
    public function getRekomendasi() { abort(501, 'Belum diimplementasi'); }
>>>>>>> 979a3705ef00246dd71606744f415d8c1390f4cb
}
