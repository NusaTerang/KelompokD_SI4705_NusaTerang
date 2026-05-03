<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDesaRequest;
use App\Models\Desa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DesaController extends Controller
{
    public function create(): View
    {
        return view('desa.input');
    }

    public function store(StoreDesaRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $action = $validated['action'] ?? 'submit';
        unset($validated['action']);

        $kondisiGabungan = $this->gabungKondisiDesa($request, $validated['kondisi_desa'] ?? '');
        unset(
            $validated['kecamatan'],
            $validated['kode_wilayah'],
            $validated['kondisi_desa'],
            $validated['jumlah_penduduk'],
            $validated['jumlah_kk'],
            $validated['status_elektrifikasi'],
            $validated['estimasi_kebutuhan_daya'],
            $validated['catatan_tambahan'],
        );

        $validated['kondisi_desa'] = $kondisiGabungan !== '' ? $kondisiGabungan : null;
        $validated['status_verifikasi'] = $action === 'draft' ? 'draft' : 'menunggu_verifikasi';
        $validated['id_admin'] = Auth::id();

        Desa::query()->create($validated);

        $message = $action === 'draft'
            ? 'Draft data desa berhasil disimpan.'
            : 'Data desa berhasil diajukan untuk verifikasi.';

        return redirect()
            ->route('desa.daftar')
            ->with('success', $message);
    }

    public function kelola(): View
    {
        $desaPrioritas = Desa::query()
            ->orderByDesc('created_at')
            ->get()
            ->values()
            ->map(function (Desa $d, int $i) {
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
            })
            ->all();

        return view('desa.kelola', compact('desaPrioritas'));
    }

    public function index(): View
    {
        $desas = Desa::query()->orderByDesc('created_at')->get();

        return view('desa.daftar', compact('desas'));
    }

    private function gabungKondisiDesa(StoreDesaRequest $request, string $teksUtama): string
    {
        $bagian = array_filter([trim($teksUtama)]);
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

        if ($baris !== []) {
            $bagian[] = implode("\n", $baris);
        }

        return trim(implode("\n\n", array_filter($bagian)));
    }

    /**
     * @return array{penduduk: int, elektrifikasi: ?string, yield_kwp: float}
     */
    private function metaDariKondisi(?string $kondisi): array
    {
        $teks = $kondisi ?? '';
        $penduduk = 0;
        if (preg_match('/Penduduk \(jiwa\):\s*(\d+)/u', $teks, $m)) {
            $penduduk = (int) $m[1];
        }
        $elektrifikasi = null;
        if (preg_match('/Status elektrifikasi:\s*(\S+)/u', $teks, $m)) {
            $elektrifikasi = $m[1];
        }
        $yield = 0.0;
        if (preg_match('/Estimasi kebutuhan daya \(kW\):\s*([0-9]+(?:\.[0-9]+)?)/u', $teks, $m)) {
            $yield = round((float) $m[1], 1);
        }

        return [
            'penduduk' => $penduduk,
            'elektrifikasi' => $elektrifikasi,
            'yield_kwp' => $yield,
        ];
    }

    private function placeholderPriorityScore(Desa $desa): float
    {
        $base = 55.0;
        $hash = crc32((string) $desa->id_desa . $desa->nama_desa);

        return round($base + ($hash % 450) / 10, 1);
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
