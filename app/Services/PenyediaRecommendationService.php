<?php

namespace App\Services;

use App\Models\PenyediaEnergi;
use App\Models\Proyek;
use Illuminate\Support\Collection;

class PenyediaRecommendationService
{
    private const MAX_DISTANCE_KM = 500;
    private const AVG_KWH_PER_KK  = 50;
    private const AVG_JIWA_PER_KK = 4;

    public function getRecommendations(Proyek $proyek): Collection
    {
        $desa = $proyek->desa;

        // Rule 4 + Rule 1: hard filters at SQL level
        $query = PenyediaEnergi::where('status', 'aktif')
            ->where('spesialisasi', $proyek->jenis_energi);

        // Rule 3: capacity filter — skip if jumlah_penduduk is null
        if ($desa->jumlah_penduduk !== null) {
            $kebutuhan = ($desa->jumlah_penduduk / self::AVG_JIWA_PER_KK) * self::AVG_KWH_PER_KK;
            $query->where(function ($q) use ($kebutuhan) {
                $q->whereNull('kapasitas_maks')
                  ->orWhere('kapasitas_maks', '>=', $kebutuhan);
            });
        }

        $penyedias = $query->get();

        // Rule 2: geographic scoring
        $desaCoords = $this->parseKoordinat($desa->koordinat ?? '');

        return $penyedias->map(function (PenyediaEnergi $penyedia) use ($desaCoords) {
            if ($desaCoords && $penyedia->latitude !== null && $penyedia->longitude !== null) {
                $distance = $this->haversine(
                    $desaCoords['lat'],
                    $desaCoords['lon'],
                    (float) $penyedia->latitude,
                    (float) $penyedia->longitude
                );
                $penyedia->match_score = max(0, round(100 - ($distance / self::MAX_DISTANCE_KM) * 100, 1));
                $penyedia->distance_km = round($distance, 1);
            } else {
                $penyedia->match_score = 50;
                $penyedia->distance_km = null;
            }

            return $penyedia;
        })->sortByDesc(function (PenyediaEnergi $p) {
            return [$p->match_score, (float) $p->rating];
        })->values();
    }

    private function parseKoordinat(string $koordinat): ?array
    {
        $parts = explode(',', $koordinat);
        if (count($parts) !== 2) {
            return null;
        }

        $lat = (float) trim($parts[0]);
        $lon = (float) trim($parts[1]);

        if ($lat === 0.0 && $lon === 0.0) {
            return null;
        }

        return ['lat' => $lat, 'lon' => $lon];
    }

    private function haversine(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;

        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
