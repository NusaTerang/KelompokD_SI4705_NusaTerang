<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
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

        $penugasan = PenugasanProyek::where('id_penugasan', $id)
            ->where('id_penyedia', $penyedia->id)
            ->firstOrFail();

        $isDraft = $request->boolean('save_draft');

        $rules = [
            'jenis_energi'             => 'required|array|min:1',
            'jenis_energi.*'           => 'in:panel_surya,mikro_hidro,biogas,hybrid_solar_baterai',
            'kapasitas_daya'           => 'required|numeric|min:0',
            'satuan_daya'              => 'required|in:kWp,kW,MW',
            'target_dana'              => 'required|numeric|min:0',
            'cost_breakdown'           => 'nullable|array',
            'cost_breakdown.*.nama'    => 'required_with:cost_breakdown|string|max:255',
            'cost_breakdown.*.nominal' => 'required_with:cost_breakdown|numeric|min:0',
            'durasi_minggu'            => 'required|integer|min:1',
            'catatan_teknis'           => 'nullable|string|max:2000',
        ];

        if ($isDraft) {
            // Relax required rules for draft
            $rules['jenis_energi']   = 'nullable|array';
            $rules['kapasitas_daya'] = 'nullable|numeric|min:0';
            $rules['satuan_daya']    = 'nullable|in:kWp,kW,MW';
            $rules['target_dana']    = 'nullable|numeric|min:0';
            $rules['durasi_minggu']  = 'nullable|integer|min:1';
        }

        $validated = $request->validate($rules);
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
}
