<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Proyek;
use App\Services\PenyediaService;
use Illuminate\Http\Request;

class PenyediaController extends Controller
{
    protected $penyediaService;

    public function __construct(PenyediaService $penyediaService)
    {
        $this->penyediaService = $penyediaService;
    }

    public function index()
    {
        $penyedia = $this->penyediaService->getAllActive();
        return response()->json(['data' => $penyedia]);
    }

    public function show($id)
    {
        $penyedia = $this->penyediaService->getById($id);
        if (!$penyedia) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json(['data' => $penyedia]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'nullable|email|unique:penyedia_energis',
            'no_telepon' => 'nullable|string|max:20',
            'spesialisasi' => 'required|in:solar,mikro_hidro,lainnya',
            'provinsi_operasi' => 'nullable|string|max:100',
            'kisaran_harga_min' => 'nullable|numeric',
            'kisaran_harga_max' => 'nullable|numeric',
        ]);

        $penyedia = $this->penyediaService->create($validated);
        return response()->json(['message' => 'Created', 'data' => $penyedia], 201);
    }

    public function recommendations(Request $request)
    {
        $request->validate([
            'proyek_id' => 'required|exists:proyeks,id'
        ]);

        $proyek = Proyek::with('desa')->findOrFail($request->proyek_id);
        $recommendations = $this->penyediaService->getRecommendations($proyek);

        return response()->json(['data' => $recommendations]);
    }
}
