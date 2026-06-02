<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proyek;
use App\Models\Desa;
use App\Notifications\ProyekDitugaskan;
use App\Services\NotificationRecipientService;
use App\Services\PenyediaRecommendationService;
use Illuminate\Support\Facades\Gate;

class ProyekController extends Controller
{
    public function index(Request $request)
    {
        $query = Proyek::with(['desa', 'fotos'])->where('status', 'aktif_funding');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhereHas('desa', fn ($dq) => $dq->where('nama_desa', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('wilayah')) {
            $query->whereHas('desa', fn ($dq) => $dq->where('provinsi', $request->wilayah));
        }

        $provinceOptions = Proyek::where('status', 'aktif_funding')
            ->whereHas('desa')
            ->join('desa', 'proyeks.desa_id', '=', 'desa.id_desa')
            ->select('desa.provinsi')
            ->distinct()
            ->orderBy('desa.provinsi')
            ->pluck('desa.provinsi');

        $projects = $query->latest('proyeks.created_at')->paginate(12)->withQueryString();

        return view('proyek.index', compact('projects', 'provinceOptions'));
    }

    public function create()
    {
        $desas = Desa::all();
        $draft_id = request()->query('draft_id');
        $proyek = $draft_id ? Proyek::find($draft_id) : null;

        return view('admin.proyek.create_step1', compact('desas', 'proyek'));
    }

    public function saveStep1(Request $request)
    {
        $draftId = $request->draft_id;
        $existingFotosCount = $draftId
            ? (Proyek::find($draftId)?->fotos()->count() ?? 0)
            : 0;
        $requireFoto = !$draftId || $existingFotosCount === 0;

        $validated = $request->validate([
            'draft_id'          => 'nullable|exists:proyeks,id',
            'judul'             => 'required|string|max:255',
            'desa_id'           => 'required|exists:desa,id_desa',
            'jenis_energi'      => 'required|in:panel_surya,mikro_hidro,biogas,hybrid_solar_baterai',
            'deskripsi'         => 'required|string',
            'estimasi_mulai'    => 'required|date',
            'estimasi_selesai'  => 'required|date|after_or_equal:estimasi_mulai',
            'fotos'             => $requireFoto ? 'required|array|min:1' : 'nullable|array',
            'fotos.*'           => 'image|max:2048',
        ]);

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
        $allPenyedia = \App\Models\PenyediaEnergi::where('status', 'aktif')->orderBy('nama')->get();

        return view('admin.proyek.create_step2', compact('proyek', 'recommendations', 'allPenyedia'));
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

    public function adminShow($id)
    {
        $proyek = Proyek::with([
            'desa',
            'penyedia',
            'fotos',
            'penugasan.detail',
            'penugasan.submittedProgressUpdates',
        ])->findOrFail($id);

        return view('admin.proyek.show', compact('proyek'));
    }

    public function review($id)
    {
        $proyek = Proyek::with(['desa', 'penyedia', 'fotos'])->findOrFail($id);
        return view('admin.proyek.create_step3', compact('proyek'));
    }

    public function edit($id)
    {
        $proyek = Proyek::findOrFail($id);
        $desas = Desa::all();
        $allPenyedia = \App\Models\PenyediaEnergi::where('status', 'aktif')->orderBy('nama')->get();

        return view('admin.proyek.edit', compact('proyek', 'desas', 'allPenyedia'));
    }

    public function update(Request $request, $id)
    {
        $proyek = Proyek::findOrFail($id);

        $validated = $request->validate([
            'judul'             => 'required|string|max:255',
            'desa_id'           => 'required|exists:desa,id_desa',
            'jenis_energi'      => 'required|in:panel_surya,mikro_hidro,biogas,hybrid_solar_baterai',
            'deskripsi'         => 'required|string',
            'estimasi_mulai'    => 'required|date',
            'estimasi_selesai'  => 'required|date|after_or_equal:estimasi_mulai',
            'penyedia_id'       => 'nullable|exists:penyedia_energis,id',
            'fotos'             => 'nullable|array',
            'fotos.*'           => 'image|max:2048',
        ]);

        $proyek->update([
            'judul' => $validated['judul'],
            'desa_id' => $validated['desa_id'],
            'jenis_energi' => $validated['jenis_energi'],
            'deskripsi' => $validated['deskripsi'],
            'estimasi_mulai' => $validated['estimasi_mulai'],
            'estimasi_selesai' => $validated['estimasi_selesai'],
            'penyedia_id' => $validated['penyedia_id'] ?? null,
        ]);

        if ($request->hasFile('fotos')) {
            $count = $proyek->fotos()->count();
            foreach ($request->file('fotos') as $foto) {
                if ($count >= 5) break;
                
                $path = $foto->store('proyek_fotos', 'public');
                $proyek->fotos()->create(['path' => $path]);
                $count++;
            }
        }

        return redirect()->route('proyek.kelola')->with('success', 'Proyek berhasil diperbarui.');
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

    public function publikasi($id)
    {
        $proyek = Proyek::with(['desa', 'penyedia', 'fotos', 'penugasan.detail'])->findOrFail($id);

        if (!in_array($proyek->status, ['draft', 'menunggu_review_admin', 'menunggu_konfirmasi_penyedia'])) {
            return redirect()->route('proyek.kelola')->with('error', 'Status proyek tidak valid untuk publikasi.');
        }

        $penugasan = $proyek->penugasan->first();
        $detail = $penugasan?->detail;

        // Auto-sinkronisasi target_dana dari input vendor + 5% platform fee
        if ($detail && (empty($proyek->target_dana) || $proyek->target_dana <= 0)) {
            $baseDana = (float)($detail->target_dana ?? 0);
            if ($baseDana > 0) {
                $platformFee = $baseDana * 0.05;
                $calculatedTargetDana = $baseDana + $platformFee;
                $proyek->update(['target_dana' => $calculatedTargetDana]);
                $proyek->refresh();
            }
        }

        // 5 kriteria kelengkapan
        $reqDetail   = !empty($proyek->judul) && !empty($proyek->deskripsi) && !empty($proyek->desa_id);
        $reqFoto     = $proyek->fotos->count() > 0;
        $reqPenyedia = !empty($proyek->penyedia_id);
        $reqRincian  = !empty($detail) && !empty($detail->kapasitas_daya);
        $reqTarget   = !empty($proyek->target_dana) && $proyek->target_dana > 0;

        $completedCount = (int)$reqDetail + (int)$reqFoto + (int)$reqPenyedia + (int)$reqRincian + (int)$reqTarget;
        $isLengkap = $completedCount === 5;

        return view('admin.proyek.publikasi', compact(
            'proyek', 'detail',
            'reqDetail', 'reqFoto', 'reqPenyedia', 'reqRincian', 'reqTarget',
            'completedCount', 'isLengkap'
        ));
    }

    public function publish(Request $request, $id)
    {
        $proyek = Proyek::with(['fotos', 'penugasan.detail'])->findOrFail($id);

        // Proyek yang sudah berjalan/selesai tidak bisa diubah
        if (in_array($proyek->status, ['eksekusi', 'selesai', 'refund'])) {
            abort(403, 'Proyek yang sedang berjalan, selesai, atau refund tidak dapat diubah statusnya.');
        }

        // UN-PUBLISH: Jika proyek sudah aktif, kembalikan ke draft
        if (in_array($proyek->status, ['aktif_funding', 'terjadwal'])) {
            $proyek->update(['status' => 'draft']);
            return redirect()->route('proyek.kelola')->with('success', 'Proyek berhasil dibatalkan publikasinya.');
        }

        // PUBLISH: Validasi kelengkapan
        $penugasan = $proyek->penugasan->first();
        $detail = $penugasan?->detail;

        if ($detail) {
            $baseDana = (float)($detail->target_dana ?? 0);
            if ($baseDana > 0) {
                $platformFee = $baseDana * 0.05;
                $calculatedTargetDana = $baseDana + $platformFee;
                $proyek->update(['target_dana' => $calculatedTargetDana]);
                $proyek->refresh();
            }
        }

        $reqDetail   = !empty($proyek->judul) && !empty($proyek->deskripsi) && !empty($proyek->desa_id);
        $reqFoto     = $proyek->fotos->count() > 0;
        $reqPenyedia = !empty($proyek->penyedia_id);
        $reqRincian  = !empty($detail) && !empty($detail->kapasitas_daya);
        $reqTarget   = !empty($proyek->target_dana) && $proyek->target_dana > 0;

        $isLengkap = $reqDetail && $reqFoto && $reqPenyedia && $reqRincian && $reqTarget;

        if (!$isLengkap) {
            return back()->withErrors(['error' => 'Proyek belum dapat dipublikasikan. Detail teknis belum lengkap.']);
        }

        $newStatus = $request->filled('jadwal_publikasi') ? 'terjadwal' : 'aktif_funding';
        $jadwal = $request->filled('jadwal_publikasi') ? \Carbon\Carbon::parse($request->jadwal_publikasi) : null;

        $proyek->update([
            'status' => $newStatus,
            'jadwal_publikasi' => $jadwal,
        ]);

        $message = $newStatus === 'aktif_funding'
            ? 'Proyek berhasil dipublikasikan.'
            : 'Proyek berhasil dijadwalkan publikasinya.';

        return redirect()->route('proyek.kelola')->with('success', $message);
    }

    public function saveDraft($id)
    {
        $proyek = Proyek::findOrFail($id);
        $proyek->update(['status' => 'draft']);
        return redirect()->route('proyek.kelola')->with('success', 'Proyek disimpan sebagai draft.');
    }

    public function kembalikan($id)
    {
        $proyek = Proyek::with('penugasan.detail')->findOrFail($id);
        $proyek->update(['status' => 'menunggu_konfirmasi_penyedia']);

        $penugasan = $proyek->penugasan->first();
        if ($penugasan && $penugasan->detail) {
            $penugasan->detail->update([
                'status' => 'draft',
                'catatan' => request('catatan_revisi')
            ]);
        }

        return redirect()->route('proyek.kelola')->with('success', 'Proyek dikembalikan ke penyedia untuk direvisi.');
    }

    public function kirimKePenyedia(Request $request, $id, NotificationRecipientService $recipients)
    {
        $proyek = Proyek::findOrFail($id);

        if (!$proyek->penyedia_id) {
            return redirect()->back()->withErrors(['penyedia' => 'Pilih penyedia terlebih dahulu sebelum mengirim.']);
        }

        $proyek->update(['status' => 'menunggu_konfirmasi_penyedia']);

        $penugasan = \App\Models\PenugasanProyek::firstOrCreate([
            'id_proyek'   => $proyek->id,
            'id_penyedia' => $proyek->penyedia_id,
        ]);

        $vendor = $recipients->vendorForProject($proyek);
        $vendor?->notify(new ProyekDitugaskan($proyek, $penugasan));

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
        $proyek = Proyek::with([
            'desa',
            'penyedia',
            'fotos',
            'donasis',
            'submittedFinalReport',
            'penugasan.submittedProgressUpdates',
            'penugasan.detail',
        ])->findOrFail($id);

        $detail = $proyek->penugasan->first()?->detail;
        $costBreakdown = $detail ? $detail->cost_breakdown : null;

        return view('proyek.show', compact('proyek', 'costBreakdown'));
    }

    public function aktifkanInstalasi(Request $request, $id)
    {
        $proyek = Proyek::findOrFail($id);

        if ($proyek->checkAndActivateInstalasi()) {
            return redirect()->back()->with('success', 'Instalasi diaktifkan! Penjadwalan timeline sedang diproses.');
        }

        return redirect()->back()->withErrors(['msg' => 'Gagal mengaktifkan instalasi. Pastikan dana sudah terkumpul dan penyedia aktif.']);
    }
}
