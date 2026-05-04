<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PenyediaEnergi;
use Illuminate\Http\Request;

class PenyediaController extends Controller
{
    public function index(Request $request)
    {
        $query = PenyediaEnergi::query();

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('spesialisasi')) {
            $query->where('spesialisasi', $request->spesialisasi);
        }

        if ($request->filled('provinsi')) {
            $query->where('provinsi_operasi', 'like', '%' . $request->provinsi . '%');
        }

        $vendors = $query->latest()->paginate(10)->withQueryString();
        $provinsiList = PenyediaEnergi::whereNotNull('provinsi_operasi')
            ->distinct()
            ->pluck('provinsi_operasi')
            ->sort()
            ->values();

        return view('admin.penyedia.index', compact('vendors', 'provinsiList'));
    }

    public function create()
    {
        return view('admin.penyedia.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'             => 'required|string|max:255',
            'email'            => 'nullable|email|unique:penyedia_energis,email',
            'no_telepon'       => 'nullable|string|max:20',
            'spesialisasi'     => 'required|in:panel_surya,mikro_hidro,biogas,hybrid_solar_baterai',
            'provinsi_operasi' => 'nullable|string|max:100',
            'kota'             => 'nullable|string|max:100',
            'latitude'         => 'nullable|numeric|between:-90,90',
            'longitude'        => 'nullable|numeric|between:-180,180',
            'kapasitas_maks'   => 'nullable|numeric|min:0',
            'kisaran_harga_min'=> 'nullable|integer|min:0',
            'kisaran_harga_max'=> 'nullable|integer|min:0',
            'deskripsi'        => 'nullable|string',
        ]);

        PenyediaEnergi::create($validated);

        return redirect()->route('admin.vendors.index')
            ->with('success', 'Penyedia energi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $vendor = PenyediaEnergi::findOrFail($id);
        return view('admin.penyedia.edit', compact('vendor'));
    }

    public function update(Request $request, $id)
    {
        $vendor = PenyediaEnergi::findOrFail($id);

        $validated = $request->validate([
            'nama'             => 'required|string|max:255',
            'email'            => 'nullable|email|unique:penyedia_energis,email,' . $id,
            'no_telepon'       => 'nullable|string|max:20',
            'spesialisasi'     => 'required|in:panel_surya,mikro_hidro,biogas,hybrid_solar_baterai',
            'provinsi_operasi' => 'nullable|string|max:100',
            'kota'             => 'nullable|string|max:100',
            'latitude'         => 'nullable|numeric|between:-90,90',
            'longitude'        => 'nullable|numeric|between:-180,180',
            'kapasitas_maks'   => 'nullable|numeric|min:0',
            'kisaran_harga_min'=> 'nullable|integer|min:0',
            'kisaran_harga_max'=> 'nullable|integer|min:0',
            'deskripsi'        => 'nullable|string',
        ]);

        $vendor->update($validated);

        return redirect()->route('admin.vendors.index')
            ->with('success', 'Data penyedia energi berhasil diperbarui.');
    }

    public function toggleStatus($id)
    {
        $vendor = PenyediaEnergi::findOrFail($id);
        $vendor->update([
            'status' => $vendor->status === 'aktif' ? 'nonaktif' : 'aktif',
        ]);

        return back()->with('success', 'Status penyedia berhasil diubah.');
    }
}
