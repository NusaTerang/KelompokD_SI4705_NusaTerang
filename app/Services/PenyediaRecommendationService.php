<?php

namespace App\Services;

use App\Models\Desa;
use App\Models\PenyediaEnergi;

class PenyediaRecommendationService
{
    /**
     * Get recommendations for a given desa based on PRD logic.
     * Weights: 50% Specialization, 30% Geography, 20% Budget
     *
     * @param Desa $desa
     * @return \Illuminate\Support\Collection
     */
    public function getRecommendations(Desa $desa)
    {
        $penyedias = PenyediaEnergi::where('status', 'aktif')->get();

        $recommended = $penyedias->map(function ($penyedia) use ($desa) {
            $score = 0;

            // 1. Spesialisasi (50%)
            if ($penyedia->spesialisasi === $desa->jenis_energi) {
                $score += 50;
            }

            // 2. Geografis (30%)
            if (strtolower($penyedia->provinsi_operasi) === strtolower($desa->provinsi)) {
                $score += 30;
            }

            // 3. Anggaran (20%)
            // Check if desa's estimasi_biaya is within the provider's min and max budget
            if ($desa->estimasi_biaya >= $penyedia->kisaran_harga_min && 
                $desa->estimasi_biaya <= $penyedia->kisaran_harga_max) {
                $score += 20;
            }

            // Add score to object for sorting purposes later
            $penyedia->match_score = $score;
            
            return $penyedia;
        });

        // Rank by score DESC, then by rating DESC
        return $recommended->sortByDesc(function ($penyedia) {
            return [$penyedia->match_score, $penyedia->rating];
        })->values(); // reset indexing
    }
}
