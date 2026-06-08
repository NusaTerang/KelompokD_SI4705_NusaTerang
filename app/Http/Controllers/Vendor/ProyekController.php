<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\DetailProyekVendor;
use App\Models\LaporanAkhirProyekVendor;
use App\Models\PenugasanProyek;
use App\Models\ProgressProyekVendor;
use App\Notifications\DetailProyekDiisi;
use App\Notifications\ProgressProyekDikirim;
use App\Notifications\ProyekSelesai;
use App\Services\NotificationRecipientService;
use Illuminate\Http\Request;

class ProyekController extends Controller
{
    public function index()
    {
        $penyedia = auth()->user()->penyedia;

        $penugasans = PenugasanProyek::with(['proyek.desa', 'proyek.fotos', 'detail'])
            ->where('id_penyedia', $penyedia->id)
            ->latest()
            ->get();

        return view('penyedia.proyek.index', compact('penugasans'));
    }

    public function show($id)
    {
        $penyedia = auth()->user()->penyedia;

        $penugasan = PenugasanProyek::with([
                'proyek.desa',
                'proyek.fotos',
                'proyek.creator',
                'detail',
                'progressDraft',
                'submittedProgressUpdates',
                'finalReportDraft',
                'submittedFinalReport',
            ])
            ->where('id_penugasan', $id)
            ->where('id_penyedia', $penyedia->id)
            ->firstOrFail();

        return view('penyedia.proyek.show', compact('penugasan'));
    }

    private function findOwnedPenugasanForProgressOrAbort($id): PenugasanProyek
    {
        $penyedia = auth()->user()->penyedia;

        if (! $penyedia) {
            abort(403);
        }

        $penugasan = PenugasanProyek::with([
                'proyek.desa',
                'proyek.fotos',
                'proyek.creator',
                'detail',
                'progressDraft',
                'submittedProgressUpdates',
                'finalReportDraft',
                'submittedFinalReport',
            ])
            ->where('id_penugasan', $id)
            ->firstOrFail();

        if ((int) $penugasan->id_penyedia !== (int) $penyedia->id) {
            abort(403);
        }

        return $penugasan;
    }

    public function progressShow($id)
    {
        $penugasan = $this->findOwnedPenugasanForProgressOrAbort($id);
        $proyek = $penugasan->proyek;
        $draft = $penugasan->progressDraft;
        $updates = $penugasan->submittedProgressUpdates;

        return view('penyedia.proyek.progress', compact('penugasan', 'proyek', 'draft', 'updates'));
    }

    public function progressStore(Request $request, $id, NotificationRecipientService $recipients)
    {
        $penugasan = $this->findOwnedPenugasanForProgressOrAbort($id);
        $proyek = $penugasan->proyek;
        $isDraft = $request->boolean('save_draft');

        $rules = [
            'persentase' => $isDraft ? 'nullable|integer|min:0|max:100' : 'required|integer|min:0|max:100',
            'deskripsi' => $isDraft ? 'nullable|string|max:2000' : 'required|string|max:2000',
            'status_progress' => $isDraft ? 'nullable|in:dijadwalkan,berjalan,selesai' : 'required|in:dijadwalkan,berjalan,selesai',
            'fotos' => 'nullable|array|max:5',
            'fotos.*' => 'image|max:2048',
        ];

        $messages = [
            'persentase.required' => 'Persentase progress wajib diisi.',
            'persentase.integer' => 'Persentase progress harus berupa angka.',
            'persentase.min' => 'Persentase progress minimal 0.',
            'persentase.max' => 'Persentase progress maksimal 100.',
            'deskripsi.required' => 'Keterangan update wajib diisi.',
            'deskripsi.max' => 'Keterangan update maksimal 2000 karakter.',
            'status_progress.required' => 'Status instalasi wajib dipilih.',
            'status_progress.in' => 'Status instalasi tidak valid.',
            'fotos.max' => 'Maksimal 5 foto progress.',
            'fotos.*.image' => 'File progress harus berupa gambar.',
            'fotos.*.max' => 'Ukuran setiap foto maksimal 2MB.',
        ];

        $validated = $request->validate($rules, $messages);

        $fotoPaths = [];
        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $foto) {
                $fotoPaths[] = $foto->store('progress_proyek_fotos', 'public');
            }
        }

        $payload = [
            'id_penugasan' => $penugasan->id_penugasan,
            'persentase' => $validated['persentase'] ?? 0,
            'deskripsi' => $validated['deskripsi'] ?? null,
            'foto_paths' => $fotoPaths ?: null,
            'status_progress' => $validated['status_progress'] ?? 'berjalan',
            'status' => $isDraft ? 'draft' : 'submitted',
            'submitted_at' => $isDraft ? null : now(),
        ];

        if ($isDraft) {
            ProgressProyekVendor::updateOrCreate(
                [
                    'id_penugasan' => $penugasan->id_penugasan,
                    'status' => 'draft',
                ],
                $payload
            );

            return redirect()
                ->route('vendor.proyek.progress.show', $penugasan->id_penugasan)
                ->with('success', 'Draft progress tersimpan.');
        }

        ProgressProyekVendor::create($payload);

        ProgressProyekVendor::where('id_penugasan', $penugasan->id_penugasan)
            ->where('status', 'draft')
            ->delete();

        $proyek->update([
            'status' => 'eksekusi',
        ]);

        $recipients->adminsAndDonorsForProject($proyek)
            ->each(fn ($recipient) => $recipient->notify(new ProgressProyekDikirim($proyek)));

        return redirect()
            ->route('vendor.proyek.progress.show', $penugasan->id_penugasan)
            ->with('success', 'Update progress berhasil dikirim dan dapat dilihat Admin/Donatur.');
    }

    public function finalReportStore(Request $request, $id, NotificationRecipientService $recipients)
    {
        $penugasan = $this->findOwnedPenugasanForProgressOrAbort($id);
        $proyek = $penugasan->proyek;
        $isDraft = $request->boolean('save_draft');

        if (! $penugasan->hasCompletedProgress()) {
            abort(403, 'Laporan akhir hanya dapat diisi setelah progress selesai 100%.');
        }

        if ($penugasan->submittedFinalReport) {
            return redirect()
                ->route('vendor.proyek.show', $penugasan->id_penugasan)
                ->withErrors(['laporan_akhir' => 'Laporan akhir sudah pernah disubmit']);
        }

        $existingDraft = $penugasan->finalReportDraft;
        $hasExistingPhotos = ! empty($existingDraft?->foto_paths);

        $rules = [
            'deskripsi' => $isDraft ? 'nullable|string|max:3000' : 'required|string|max:3000',
            'kapasitas_terpasang' => $isDraft ? 'nullable|numeric|min:0' : 'required|numeric|min:0',
            'satuan_kapasitas' => $isDraft ? 'nullable|in:kWh,kWp' : 'required|in:kWh,kWp',
            'catatan' => 'nullable|string|max:2000',
            'fotos' => ($isDraft || $hasExistingPhotos) ? 'nullable|array|max:5' : 'required|array|min:1|max:5',
            'fotos.*' => 'image|max:2048',
        ];

        $messages = [
            'deskripsi.required' => 'Deskripsi laporan wajib diisi',
            'deskripsi.max' => 'Deskripsi laporan maksimal 3000 karakter.',
            'kapasitas_terpasang.required' => 'Kapasitas terpasang wajib diisi.',
            'kapasitas_terpasang.numeric' => 'Kapasitas terpasang harus berupa angka.',
            'kapasitas_terpasang.min' => 'Kapasitas terpasang minimal 0.',
            'satuan_kapasitas.required' => 'Satuan kapasitas wajib dipilih.',
            'satuan_kapasitas.in' => 'Satuan kapasitas tidak valid.',
            'fotos.required' => 'Minimal 1 foto dokumentasi akhir wajib diunggah',
            'fotos.min' => 'Minimal 1 foto dokumentasi akhir wajib diunggah',
            'fotos.max' => 'Maksimal 5 foto dokumentasi akhir.',
            'fotos.*.image' => 'File dokumentasi akhir harus berupa gambar.',
            'fotos.*.max' => 'Ukuran setiap foto maksimal 2MB.',
        ];

        $validated = $request->validate($rules, $messages);

        $fotoPaths = $existingDraft?->foto_paths ?? [];
        if ($request->hasFile('fotos')) {
            $fotoPaths = [];
            foreach ($request->file('fotos') as $foto) {
                $fotoPaths[] = $foto->store('laporan_akhir_proyek_fotos', 'public');
            }
        }

        $payload = [
            'id_penugasan' => $penugasan->id_penugasan,
            'id_proyek' => $proyek->id,
            'id_penyedia' => $penugasan->id_penyedia,
            'deskripsi' => $validated['deskripsi'] ?? null,
            'kapasitas_terpasang' => $validated['kapasitas_terpasang'] ?? null,
            'satuan_kapasitas' => $validated['satuan_kapasitas'] ?? 'kWp',
            'foto_paths' => $fotoPaths ?: null,
            'catatan' => $validated['catatan'] ?? null,
            'status' => $isDraft ? 'draft' : 'submitted',
            'submitted_at' => $isDraft ? null : now(),
        ];

        if ($isDraft) {
            LaporanAkhirProyekVendor::updateOrCreate(
                [
                    'id_penugasan' => $penugasan->id_penugasan,
                    'status' => 'draft',
                ],
                $payload
            );

            return redirect()
                ->route('vendor.proyek.show', $penugasan->id_penugasan)
                ->with('success', 'Draft laporan akhir tersimpan.');
        }

        LaporanAkhirProyekVendor::create($payload);

        LaporanAkhirProyekVendor::where('id_penugasan', $penugasan->id_penugasan)
            ->where('status', 'draft')
            ->delete();

        $proyek->update(['status' => 'selesai']);

        $recipients->adminsAndDonorsForProject($proyek)
            ->each(fn ($recipient) => $recipient->notify(new ProyekSelesai($proyek)));

        return redirect()
            ->route('vendor.proyek.show', $penugasan->id_penugasan)
            ->with('success', 'Laporan akhir berhasil dikirim. Proyek ditandai selesai.');
    }

    public function saveDetail(Request $request, $id, NotificationRecipientService $recipients)
    {
        $penyedia = auth()->user()->penyedia;

        $penugasan = PenugasanProyek::with('proyek')
            ->where('id_penugasan', $id)
            ->where('id_penyedia', $penyedia->id)
            ->firstOrFail();

        $isDraft = $request->boolean('save_draft');

        // Hapus item kosong dari cost_breakdown agar tidak gagal validasi
        if ($request->has('cost_breakdown')) {
            $cleaned = array_values(array_filter(
                $request->input('cost_breakdown', []),
                fn ($item) => !empty($item['nama']) || !empty($item['nominal'])
            ));
            $request->merge(['cost_breakdown' => $cleaned]);
        }

        $rules = [
            'kapasitas_daya'           => 'required|numeric|min:0',
            'satuan_daya'              => 'required|in:kWp,kW,MW',
            'target_dana'              => 'required|numeric|min:0',
            'cost_breakdown'           => 'nullable|array',
            'cost_breakdown.*.nama'    => 'required_with:cost_breakdown|string|max:255',
            'cost_breakdown.*.nominal' => 'required_with:cost_breakdown|numeric|min:0',
            'durasi_minggu'            => 'required|integer|min:1',
            'catatan_teknis'           => 'nullable|string|max:2000',
        ];

        $messages = [
            'kapasitas_daya.required'     => 'Kapasitas daya wajib diisi.',
            'kapasitas_daya.numeric'      => 'Kapasitas daya harus berupa angka.',
            'satuan_daya.required'        => 'Satuan daya wajib dipilih.',
            'satuan_daya.in'              => 'Satuan daya tidak valid.',
            'target_dana.required'        => 'Target dana wajib diisi.',
            'target_dana.numeric'         => 'Target dana harus berupa angka.',
            'cost_breakdown.*.nama.required_with'    => 'Nama item cost breakdown wajib diisi jika nominal diisi.',
            'cost_breakdown.*.nominal.required_with' => 'Nominal item cost breakdown wajib diisi jika nama diisi.',
            'cost_breakdown.*.nominal.numeric'       => 'Nominal harus berupa angka.',
            'durasi_minggu.required'      => 'Durasi pengerjaan wajib diisi.',
            'durasi_minggu.integer'       => 'Durasi harus berupa bilangan bulat.',
            'catatan_teknis.max'          => 'Catatan teknis maksimal 2000 karakter.',
        ];

        if ($isDraft) {
            $rules['kapasitas_daya'] = 'nullable|numeric|min:0';
            $rules['satuan_daya']    = 'nullable|in:kWp,kW,MW';
            $rules['target_dana']    = 'nullable|numeric|min:0';
            $rules['durasi_minggu']  = 'nullable|integer|min:1';
        }

        $validated = $request->validate($rules, $messages);
        $validated['jenis_energi'] = [$penugasan->proyek->jenis_energi];
        $validated['status'] = $isDraft ? 'draft' : 'submitted';

        DetailProyekVendor::updateOrCreate(
            ['id_penugasan' => $penugasan->id_penugasan],
            $validated
        );

        if (! $isDraft) {
            $penugasan->update([
                'status_penugasan' => 'diterima',
                'tanggal_respon' => now(),
            ]);

            $penugasan->proyek->update(['status' => 'menunggu_review_admin']);

            $recipients->admins()
                ->each(fn ($admin) => $admin->notify(new DetailProyekDiisi($penugasan->proyek)));
        }

        if ($isDraft) {
            return redirect()->route('vendor.proyek.show', $id)->with('success', 'Draft tersimpan.');
        }

        return redirect()->route('vendor.proyek.index')->with('success', 'Rincian berhasil dikirim ke Admin untuk ditinjau!');
    }

    public function expiryDecisionShow($id)
    {
        $penyedia = auth()->user()->penyedia;

        $penugasan = PenugasanProyek::with(['proyek.desa'])
            ->where('id_penugasan', $id)
            ->where('id_penyedia', $penyedia->id)
            ->firstOrFail();

        $proyek = $penugasan->proyek;

        if ($proyek->status !== 'menunggu_keputusan_vendor' || ! $proyek->expired_extension_pending) {
            abort(403, 'Proyek tidak menunggu keputusan vendor.');
        }

        return view('penyedia.proyek.expiry_decision', compact('penugasan', 'proyek'));
    }

    public function expiryDecision(Request $request, $id)
    {
        $validated = $request->validate([
            'decision' => 'required|in:refund,continue',
        ]);

        $penyedia = auth()->user()->penyedia;

        $penugasan = PenugasanProyek::with('proyek')
            ->where('id_penugasan', $id)
            ->where('id_penyedia', $penyedia->id)
            ->firstOrFail();

        $proyek = $penugasan->proyek;

        if ($proyek->status !== 'menunggu_keputusan_vendor' || ! $proyek->expired_extension_pending) {
            abort(403, 'Proyek tidak menunggu keputusan vendor.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($proyek, $validated) {
            $proyek->update([
                'status' => $validated['decision'] === 'refund' ? 'refund' : 'selesai',
                'expired_vendor_decision' => $validated['decision'],
                'expired_extension_pending' => false,
            ]);

            if ($validated['decision'] === 'refund') {
                $donasis = \App\Models\Donasi::where('id_proyek', $proyek->id)
                    ->where('status', 'success')
                    ->get();

                foreach ($donasis as $donasi) {
                    $donatur = $donasi->donatur;
                    if ($donatur) {
                        $donatur->tambahSaldo($donasi->nominal, "Pengembalian dana donasi untuk proyek: {$proyek->judul}");
                    }
                    $donasi->update(['status' => 'refunded']);
                }
            }
        });

        $message = $validated['decision'] === 'refund'
            ? 'Status proyek diubah menjadi refund.'
            : 'Proyek dilanjutkan dengan dana terkumpul saat ini.';

        return redirect()->route('vendor.proyek.index')->with('success', $message);
    }

    public function mintaKlarifikasi(Request $request, $id)
    {
        $request->validate(['pertanyaan' => 'required|string|max:1000']);

        return redirect()->back()->with('success', 'Permintaan klarifikasi terkirim ke Admin.');
    }

    public function laporkanKendala(Request $request, $id)
    {
        $request->validate(['kendala' => 'required|string|max:1000']);

        return redirect()->back()->with('info', 'Laporan kendala telah diterima.');
    }
}
