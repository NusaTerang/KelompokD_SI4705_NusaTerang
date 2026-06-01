<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDesaRequest;
use App\Models\Desa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\Request;

class DesaController extends Controller
{
    public function index(): View
    {
        $desas = Desa::query()->orderBy('created_at')->get();
        return view('admin.desa.daftar', compact('desas'));
    }

    public function create(): View
    {
        $aktivitasTerakhir = Desa::orderBy('created_at', 'desc')->take(5)->get();
        return view('admin.desa.input', compact('aktivitasTerakhir'));
    }

    public function store(StoreDesaRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        
        // Ambil action (submit/draft) lalu hapus dari array agar tidak masuk ke create()
        $action = $request->input('action', 'submit');
        unset($validated['action']);

        // Gabungkan data input tambahan ke kolom kondisi_desa (Textarea)
        $kondisiGabungan = $this->gabungKondisiDesa($request, $validated['kondisi_desa'] ?? '');

        $dataToSave = collect($validated)->except([
            'kecamatan', 'kode_wilayah', 'kondisi_desa', 
            'jumlah_kk', 'status_elektrifikasi', 'estimasi_kebutuhan_daya', 'catatan_tambahan'
        ])->toArray();

        // Mapping data ke kolom tabel sesuai migrasi
        $dataToSave['kondisi_desa'] = $kondisiGabungan !== '' ? $kondisiGabungan : null;
        $dataToSave['id_admin'] = Auth::id();

        Desa::create($dataToSave);

        $message = ($action === 'draft')
            ? 'Draft data desa berhasil disimpan.'
            : 'Data desa berhasil diajukan untuk verifikasi.';

        return redirect()->route('desa.daftar')->with('success', $message);
    }

    public function show($id): View
    {
        $desa = Desa::findOrFail($id);
        return view('admin.desa.show', compact('desa'));
    }

    public function edit($id): View
    {
        $desa = Desa::findOrFail($id);
        return view('admin.desa.edit', compact('desa'));
    }

    public function update(StoreDesaRequest $request, $id): RedirectResponse
    {
        $desa = Desa::findOrFail($id);
        $validated = $request->validated();

        $action = $request->input('action', 'submit');
        unset($validated['action']);

        $kondisiGabungan = $this->gabungKondisiDesa($request, $validated['kondisi_desa'] ?? '');

        $dataToUpdate = collect($validated)->except([
            'kecamatan', 'kode_wilayah', 'kondisi_desa', 
            'jumlah_kk', 'status_elektrifikasi', 'estimasi_kebutuhan_daya', 'catatan_tambahan'
        ])->toArray();

        $dataToUpdate['kondisi_desa'] = $kondisiGabungan !== '' ? $kondisiGabungan : null;

        $desa->update($dataToUpdate);

        return redirect()->route('desa.daftar')->with('success', 'Data desa berhasil diperbarui.');
    }

    public function destroy($id): RedirectResponse
    {
        $desa = Desa::findOrFail($id);
        $desa->delete();
        return redirect()->route('desa.daftar')->with('success', 'Data desa berhasil dihapus.');
    }

    /**
     * Logic untuk menggabungkan berbagai field input menjadi satu string teks di kondisi_desa
     */
    private function gabungKondisiDesa(Request $request, string $teksUtama): string
    {
        $tambah = static fn (?string $nilai, string $label) => ($nilai !== null && $nilai !== '')
            ? "{$label}: {$nilai}"
            : null;

        $baris = array_filter([
            $tambah($request->input('kecamatan'), 'Kecamatan'),
            $tambah($request->input('kode_wilayah'), 'Kode wilayah'),
            $request->filled('jumlah_penduduk') ? 'Penduduk (jiwa): ' . (int) $request->input('jumlah_penduduk') : null,
            $request->filled('jumlah_kk') ? 'Jumlah KK: ' . (int) $request->input('jumlah_kk') : null,
            $request->filled('status_elektrifikasi') ? 'Status elektrifikasi: ' . $request->input('status_elektrifikasi') : null,
            $tambah($request->input('estimasi_kebutuhan_daya'), 'Estimasi kebutuhan daya (kW)'),
            $tambah($request->input('catatan_tambahan'), 'Catatan tambahan'),
        ]);

        $hasil = trim($teksUtama);
        if ($baris !== []) {
            $hasil .= ($hasil !== '' ? "\n\n" : "") . implode("\n", $baris);
        }

        return $hasil;
    }

    public function kelola(Request $request)
    {
        $query = Desa::query()->withExists('proyeks');

        if ($request->filled('q')) {
            $query->where('nama_desa', 'like', '%' . $request->q . '%');
        }

        if ($request->filled('provinsi')) {
            $query->where('provinsi', $request->provinsi);
        }

        $desas = $query->get();
        $availableProvinces = Desa::select('provinsi')->whereNotNull('provinsi')->distinct()->pluck('provinsi')->sort();

        $desaPrioritas = $desas->map(function ($d) {
            $skor = 0;
            // 1. Rasio Elektrifikasi (Max 30)
            if (str_contains(strtolower($d->kondisi_desa ?? ''), 'belum_teraliri')) $skor += 30;
            elseif (str_contains(strtolower($d->kondisi_desa ?? ''), 'sebagian')) $skor += 20;
            
            // 2. Populasi Produktif (Max 25)
            $populasiProduktif = $d->jumlah_penduduk * 0.67;
            $skor += min(($populasiProduktif / 500) * 25, 25);

            // 3. Potensi Surya (Max 25)
            if ($d->koordinat) {
                $lat = abs((float) explode(',', $d->koordinat)[0]);
                if ($lat <= 3.0) $skor += 25;
                elseif ($lat <= 6.0) $skor += 20;
                else $skor += 15;
            }
            
            // 4. Kesiapan Infrastruktur (Max 20)
            $kesiapan_infrastruktur = !empty($d->kondisi_desa) ? 15 : 10;
            $skor += $kesiapan_infrastruktur;

            $statusListrik = str_contains(strtolower($d->kondisi_desa ?? ''), 'belum_teraliri') ? 'OFF-GRID' 
                : (str_contains(strtolower($d->kondisi_desa ?? ''), 'sebagian') ? 'TERBATAS' : 'TERALIRI');

            $tipeEnergi = match (strtolower($d->sumber ?? '')) {
                'solar_panel', 'panel_surya' => 'panel_surya',
                'mikro_hidro' => 'mikro_hidro',
                'biogas' => 'biogas',
                'hybrid_solar_baterai', 'hybrid' => 'hybrid_solar_baterai',
                default => 'lainnya',
            };

            return [
                'id_desa' => $d->id_desa,
                'nama_desa' => $d->nama_desa,
                'skor_prioritas' => min($skor, 100), // Max 100
                'populasi' => $d->jumlah_penduduk,
                'lokasi' => $d->kabupaten . ', ' . $d->provinsi,
                'status_listrik' => $statusListrik,
                'tipe_energi' => $tipeEnergi,
                'yield_kwp' => 0,
                'gambar' => 'https://picsum.photos/seed/desa' . $d->id_desa . '/640/360',
                'solar_optimized' => $d->sumber === 'solar_panel',
                'url_detail' => route('desa.show', $d->id_desa),
                'url_proyek' => route('proyek.create', ['desa_id' => $d->id_desa]),
                'has_project' => $d->proyeks_exists,
            ];
        });

        if ($request->filled('status_listrik')) {
            $filterStatus = strtoupper($request->status_listrik);
            $desaPrioritas = $desaPrioritas->filter(fn($d) => strtoupper($d['status_listrik']) === $filterStatus);
        }

        if ($request->filled('tipe_energi')) {
            $filterEnergi = strtolower($request->tipe_energi);
            $desaPrioritas = $desaPrioritas->filter(fn($d) => strtolower($d['tipe_energi']) === $filterEnergi);
        }

        $desaPrioritas = $desaPrioritas->sortByDesc('skor_prioritas')->values();

        $desaPrioritas = $desaPrioritas->map(function ($item, $index) {
            $item['rank'] = $index + 1;
            return $item;
        });

        return view('admin.desa.kelola', compact('desaPrioritas', 'availableProvinces'));
    }
}