<?php

namespace App\Services;

use App\Models\Proyek;
use Illuminate\Support\Facades\DB;

class ProyekService
{
    public function getAll()
    {
        return Proyek::with(['desa', 'penyedia'])->get();
    }

    public function getById($id)
    {
        return Proyek::with(['desa', 'penyedia', 'fotos', 'creator'])->findOrFail($id);
    }

    public function createStep1(array $data, $userId): Proyek
    {
        return DB::transaction(function () use ($data, $userId) {
            $proyek = Proyek::create([
                'desa_id' => $data['desa_id'],
                'judul' => $data['judul'],
                'deskripsi' => $data['deskripsi'],
                'jenis_energi' => $data['jenis_energi'] ?? 'solar',
                'estimasi_mulai' => $data['estimasi_mulai'],
                'estimasi_selesai' => $data['estimasi_selesai'],
                'status' => 'draft',
                'created_by' => $userId,
            ]);

            if (isset($data['fotos']) && is_array($data['fotos'])) {
                foreach ($data['fotos'] as $index => $fotoPath) {
                    $proyek->fotos()->create([
                        'path' => $fotoPath,
                        'urutan' => $index + 1
                    ]);
                }
            }

            return $proyek;
        });
    }

    public function assignPenyedia(Proyek $proyek, $penyediaId): Proyek
    {
        $proyek->update([
            'penyedia_id' => $penyediaId
        ]);
        return $proyek;
    }

    public function submitToPenyedia(Proyek $proyek): Proyek
    {
        $proyek->update([
            'status' => 'menunggu_konfirmasi_penyedia'
        ]);
        return $proyek;
    }
}
