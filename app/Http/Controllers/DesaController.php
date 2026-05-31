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
        return view('admin.desa.input');
    }

    public function store(StoreDesaRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        
        // Ambil action (submit/draft) lalu hapus dari array agar tidak masuk ke create()
        $action = $request->input('action', 'submit');
        unset($validated['action']);

        // Gabungkan data input tambahan ke kolom kondisi_desa (Textarea)
        $kondisiGabungan = $this->gabungKondisiDesa($request, $validated['kondisi_desa'] ?? '');

        // Bersihkan field yang tidak ada di tabel database (karena digabung ke kondisi_desa)
        $dataToSave = collect($validated)->except([
            'kecamatan', 'kode_wilayah', 'kondisi_desa', 'jumlah_penduduk', 
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
            'kecamatan', 'kode_wilayah', 'kondisi_desa', 'jumlah_penduduk', 
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

    public function kelola(): View
    {
        $desas = Desa::query()->orderByDesc('created_at')->get();
        
        $desaPrioritas = $desas->map(function (Desa $d, int $i) {
            $meta = $this->metaDariKondisi($d->kondisi_desa);
            return [
                'rank' => $i + 1,
                'nama_desa' => $d->nama_desa,
                'lokasi' => $d->kabupaten . ', ' . $d->provinsi,
                'skor_prioritas' => $this->placeholderPriorityScore($d),
                'populasi' => $meta['penduduk'],
                'status_listrik' => $this->elektrifikasiLabel($meta['elektrifikasi']),
                'yield_kwp' => $meta['yield_kwp'],
                'gambar' => 'https://picsum.photos/seed/desa' . $d->id_desa . '/640/360',
                'solar_optimized' => $d->sumber === 'solar_panel',
                'url_detail' => '#',
                'url_proyek' => '#',
            ];
        })->toArray();

        return view('admin.desa.kelola', compact('desaPrioritas'));
    }

    private function metaDariKondisi(?string $kondisi): array
    {
        $teks = $kondisi ?? '';
        preg_match('/Penduduk \(jiwa\):\s*(\d+)/u', $teks, $m1);
        preg_match('/Status elektrifikasi:\s*(\S+)/u', $teks, $m2);
        preg_match('/Estimasi kebutuhan daya \(kW\):\s*([0-9]+(?:\.[0-9]+)?)/u', $teks, $m3);

        return [
            'penduduk' => (int)($m1[1] ?? 0),
            'elektrifikasi' => $m2[1] ?? null,
            'yield_kwp' => round((float)($m3[1] ?? 0), 1),
        ];
    }

    private function placeholderPriorityScore(Desa $desa): float
    {
        return round(55.0 + (crc32($desa->id_desa . $desa->nama_desa) % 450) / 10, 1);
    }

    private function elektrifikasiLabel(?string $status): string
    {
        return match ($status) {
            'belum_teraliri' => 'OFF-GRID',
            'sebagian' => 'TERBATAS',
            'sudah_teraliri' => 'TERALIRI',
            default => '—',
        };
    }
}