<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proyek;
use App\Models\Desa;
use App\Services\PenyediaRecommendationService;

class ProyekController extends Controller
{
    public function create()
    {
        $desas = Desa::all();
        $draft_id = request()->query('draft_id');
        $proyek = $draft_id ? Proyek::find($draft_id) : null;

        return view('admin.proyek.create_step1', compact('desas', 'proyek'));
    }

    public function saveStep1(Request $request)
    {
        $validated = $request->validate([
            'draft_id' => 'nullable|exists:proyeks,id',
            'judul' => 'required|string|max:255',
            'desa_id' => 'required|exists:desa,id_desa',
            'jenis_energi' => 'required|in:panel_surya,mikro_hidro,biogas,hybrid_solar_baterai',
            'deskripsi' => 'nullable|string',
            'estimasi_mulai' => 'nullable|date',
            'estimasi_selesai' => 'nullable|date|after_or_equal:estimasi_mulai',
            'fotos.*' => 'image|max:2048'
        ]);

        // Fix logic for checkbox/radio that might not exist in form if unchecked
        // But for required fields it's fine.

        $proyek = Proyek::updateOrCreate(
            ['id' => $request->draft_id],
            [
                'judul' => $validated['judul'],
                'desa_id' => $validated['desa_id'],
                'jenis_energi' => $validated['jenis_energi'],
                'deskripsi' => $validated['deskripsi'] ?? null,
                'estimasi_mulai' => $validated['estimasi_mulai'] ?? null,
                'estimasi_selesai' => $validated['estimasi_selesai'] ?? null,
                'status' => 'draft',
                'created_by' => 1 // Hardcode for now since auth may not exist
            ]
        );

        if ($request->hasFile('fotos')) {
            $count = $proyek->fotos()->count();
            foreach ($request->file('fotos') as $foto) {
                if ($count >= 5) break;
                
                $path = $foto->store('proyek_fotos', 'public');
                $proyek->fotos()->create(['path' => $path]);
                $count++;
            }
        }

        return redirect()->route('proyek.step2', ['id' => $proyek->id]);
    }

    public function step2($id, PenyediaRecommendationService $service)
    {
        $proyek = Proyek::with('desa')->findOrFail($id);

        if (!$proyek->desa) {
            return redirect()->route('proyek.create', ['draft_id' => $proyek->id])
                ->withErrors(['desa_id' => 'Desa tidak ditemukan. Pilih desa terlebih dahulu.']);
        }

        $recommendations = $service->getRecommendations($proyek);

        return view('admin.proyek.create_step2', compact('proyek', 'recommendations'));
    }

    public function saveStep2(Request $request, $id)
    {
        $proyek = Proyek::findOrFail($id);
        $request->validate([
            'penyedia_id' => 'required|exists:penyedia_energis,id'
        ]);

        $proyek->update([
            'penyedia_id' => $request->penyedia_id
        ]);

        return redirect()->route('proyek.review', ['id' => $proyek->id]);
    }

    public function review($id)
    {
        $proyek = Proyek::with(['desa', 'penyedia', 'fotos'])->findOrFail($id);
        return view('admin.proyek.create_step3', compact('proyek'));
    }

    public function kelola(Request $request)
    {
        $query = Proyek::with(['desa', 'penyedia', 'fotos']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhereHas('desa', fn ($dq) => $dq->where('nama_desa', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('jenis_energi')) {
            $query->where('jenis_energi', $request->jenis_energi);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $proyeks = $query->latest()->paginate(15)->withQueryString();

        return view('admin.proyek.kelola', compact('proyeks'));
    }

    public function publish($id)
    {
        $proyek = Proyek::findOrFail($id);

        if (in_array($proyek->status, ['eksekusi', 'selesai'])) {
            abort(403, 'Proyek yang sedang berjalan atau selesai tidak dapat diubah statusnya.');
        }

        $newStatus = $proyek->status === 'aktif_funding' ? 'draft' : 'aktif_funding';
        $proyek->update(['status' => $newStatus]);

        $message = $newStatus === 'aktif_funding'
            ? 'Proyek berhasil dipublikasikan.'
            : 'Proyek berhasil dibatalkan publikasinya.';

        return redirect()->route('proyek.kelola')->with('success', $message);
    }

    public function kirimKePenyedia(Request $request, $id)
    {
        $proyek = Proyek::findOrFail($id);
        $proyek->update([
            'status' => 'menunggu_konfirmasi_penyedia'
        ]);

        return redirect()->route('proyek.kelola')->with('success', 'Proyek berhasil dikirim ke penyedia!');
    }

    public function destroy($id)
    {
        $proyek = Proyek::findOrFail($id);

        if ($proyek->status !== 'draft') {
            abort(403, 'Hanya proyek berstatus draft yang dapat dihapus.');
        }

        $proyek->fotos()->each(function ($foto) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($foto->path);
        });
        $proyek->fotos()->delete();
        $proyek->delete();

        return redirect()->route('proyek.kelola')->with('success', 'Proyek berhasil dihapus.');
    }

    public function show($id)
    {
        $proyek = Proyek::with(['desa', 'penyedia', 'fotos'])->findOrFail($id);
        return view('proyek.show', compact('proyek'));
    }
}
