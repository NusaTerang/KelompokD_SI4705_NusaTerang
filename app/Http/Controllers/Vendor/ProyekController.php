<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
<<<<<<< HEAD
use App\Models\DetailProyekVendor;
use App\Models\PenugasanProyek;
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

        $penugasan = PenugasanProyek::with(['proyek.desa', 'proyek.fotos', 'proyek.creator', 'detail'])
            ->where('id_penugasan', $id)
            ->where('id_penyedia', $penyedia->id)
            ->firstOrFail();

        return view('penyedia.proyek.show', compact('penugasan'));
    }

    public function saveDetail(Request $request, $id)
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
            // cost_breakdown sudah dibersihkan di atas, jika kosong menjadi null dan lolos validasi
        }

        $validated = $request->validate($rules, $messages);
        $validated['jenis_energi'] = [$penugasan->proyek->jenis_energi];
        $validated['status'] = $isDraft ? 'draft' : 'submitted';

        DetailProyekVendor::updateOrCreate(
            ['id_penugasan' => $penugasan->id_penugasan],
            $validated
        );

        if (! $isDraft) {
            $penugasan->proyek->update(['status' => 'menunggu_review_admin']);
        }

        $msg = $isDraft ? 'Draft tersimpan.' : 'Rincian berhasil dikirim ke Admin untuk ditinjau!';

        return redirect()->route('vendor.proyek.show', $id)->with('success', $msg);
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

        $proyek->update([
            'status' => $validated['decision'] === 'refund' ? 'refund' : 'selesai',
            'expired_vendor_decision' => $validated['decision'],
            'expired_extension_pending' => false,
        ]);

        $message = $validated['decision'] === 'refund'
            ? 'Status proyek diubah menjadi refund.'
            : 'Proyek dilanjutkan dengan dana terkumpul saat ini.';

        return redirect()->route('vendor.proyek.index')->with('success', $message);
    }

    public function mintaKlarifikasi(Request $request, $id)
    {
        $request->validate(['pertanyaan' => 'required|string|max:1000']);

        // TODO: store klarifikasi / send notification
        return redirect()->back()->with('success', 'Permintaan klarifikasi terkirim ke Admin.');
    }

    public function laporkanKendala(Request $request, $id)
    {
        $request->validate(['kendala' => 'required|string|max:1000']);

        // TODO: store kendala / send notification
        return redirect()->back()->with('info', 'Laporan kendala telah diterima.');
    }
=======
use Illuminate\Http\Request;

/** Stub — akan diimplementasi oleh tim */
class ProyekController extends Controller
{
    public function index() { abort(501, 'Belum diimplementasi'); }
    public function show($id) { abort(501, 'Belum diimplementasi'); }
    public function expiryDecisionShow($id) { abort(501, 'Belum diimplementasi'); }
    public function saveDetail(Request $request, $id) { abort(501, 'Belum diimplementasi'); }
    public function expiryDecision(Request $request, $id) { abort(501, 'Belum diimplementasi'); }
    public function mintaKlarifikasi(Request $request, $id) { abort(501, 'Belum diimplementasi'); }
    public function laporkanKendala(Request $request, $id) { abort(501, 'Belum diimplementasi'); }
>>>>>>> 979a3705ef00246dd71606744f415d8c1390f4cb
}
