<?php

namespace App\Services;

use App\Models\PenyediaEnergi;
use App\Models\Proyek;
use Illuminate\Support\Collection;

class PenyediaService
{
    public function getAllActive(): Collection
    {
        return PenyediaEnergi::where('status', 'aktif')->get();
    }

    public function getById($id): ?PenyediaEnergi
    {
        return PenyediaEnergi::find($id);
    }

    public function create(array $data): PenyediaEnergi
    {
        return PenyediaEnergi::create($data);
    }

    public function update(PenyediaEnergi $penyedia, array $data): bool
    {
        return $penyedia->update($data);
    }

    /**
     * Rule-based matching from PBI-10
     */
    public function getRecommendations(Proyek $proyek): Collection
    {
        $desa = $proyek->desa;
        
        return PenyediaEnergi::where('status', 'aktif')
            ->get()
            ->map(function ($penyedia) use ($proyek, $desa) {
                $skor = 0;

                // Spesialisasi (bobot 50)
                if ($penyedia->spesialisasi === $proyek->jenis_energi) {
                    $skor += 50;
                }

                // Kedekatan geografis (bobot 30)
                if ($penyedia->provinsi_operasi === $desa->provinsi) {
                    $skor += 30;
                }

                // Kesesuaian anggaran (bobot 20)
                if ($desa->estimasi_biaya >= $penyedia->kisaran_harga_min 
                    && $desa->estimasi_biaya <= $penyedia->kisaran_harga_max) {
                    $skor += 20;
                }

                $penyedia->skor_kesesuaian = $skor;
                return $penyedia;
            })
            ->sortByDesc('skor_kesesuaian')
            ->values();
    }
}
