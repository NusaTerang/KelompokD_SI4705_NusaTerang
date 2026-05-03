<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Proyek;
use App\Services\ProyekService;
use Illuminate\Http\Request;

class ProyekController extends Controller
{
    protected $proyekService;

    public function __construct(ProyekService $proyekService)
    {
        $this->proyekService = $proyekService;
    }

    public function index()
    {
        $proyeks = $this->proyekService->getAll();
        return response()->json(['data' => $proyeks]);
    }

    public function show($id)
    {
        $proyek = $this->proyekService->getById($id);
        return response()->json(['data' => $proyek]);
    }

    // Wizard Step 1: Create Basic Info
    public function createStep1(Request $request)
    {
        $validated = $request->validate([
            'desa_id' => 'required|exists:desa,id_desa',
            'judul' => 'required|string|min:10|max:255',
            'deskripsi' => 'required|string|min:50',
            'jenis_energi' => 'nullable|in:solar,mikro_hidro,lainnya',
            'estimasi_mulai' => 'required|date',
            'estimasi_selesai' => 'required|date|after:estimasi_mulai',
            'fotos' => 'nullable|array',
            'fotos.*' => 'string'
        ]);

        $proyek = $this->proyekService->createStep1($validated, $request->user()->id);
        return response()->json(['message' => 'Step 1 saved', 'data' => $proyek], 201);
    }

    // Wizard Step 2: Assign Penyedia
    public function saveStep2(Request $request, $id)
    {
        $request->validate([
            'penyedia_id' => 'required|exists:penyedia_energis,id'
        ]);

        $proyek = Proyek::findOrFail($id);
        $proyek = $this->proyekService->assignPenyedia($proyek, $request->penyedia_id);
        
        return response()->json(['message' => 'Step 2 saved', 'data' => $proyek]);
    }

    // Wizard Step 3: Submit
    public function submit($id)
    {
        $proyek = Proyek::findOrFail($id);
        $proyek = $this->proyekService->submitToPenyedia($proyek);
        
        return response()->json(['message' => 'Project submitted', 'data' => $proyek]);
    }
}
