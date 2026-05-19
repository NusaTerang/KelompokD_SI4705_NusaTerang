<?php

namespace Tests\Unit;

use App\Models\Desa;
use App\Models\PenyediaEnergi;
use App\Models\Proyek;
use App\Services\PenyediaRecommendationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PenyediaRecommendationServiceTest extends TestCase
{
    use RefreshDatabase;

    private PenyediaRecommendationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PenyediaRecommendationService();
    }

    private function makeDesa(array $attrs = []): Desa
    {
        return Desa::create(array_merge([
            'nama_desa'         => 'Desa Test',
            'provinsi'          => 'Jawa Barat',
            'kabupaten'         => 'Bandung',
            'koordinat'         => '-6.917464,107.619123',
            'jumlah_penduduk'   => 400,
            'sumber'            => 'solar_panel',
        ], $attrs));
    }

    private function makePenyedia(array $attrs = []): PenyediaEnergi
    {
        return PenyediaEnergi::create(array_merge([
            'nama'           => 'Vendor Test',
            'spesialisasi'   => 'panel_surya',
            'status'         => 'aktif',
            'kapasitas_maks' => 10000,
            'latitude'       => -6.900000,
            'longitude'      => 107.600000,
            'rating'         => 4.5,
        ], $attrs));
    }

    private function makeProyek(Desa $desa, string $jenisEnergi = 'panel_surya'): Proyek
    {
        return Proyek::create([
            'desa_id'      => $desa->id_desa,
            'judul'        => 'Test Proyek',
            'jenis_energi' => $jenisEnergi,
            'status'       => 'draft',
            'created_by'   => null,
        ]);
    }

    /** Rule 4: nonaktif vendors excluded */
    public function test_excludes_nonaktif_vendors(): void
    {
        $desa = $this->makeDesa();
        $this->makePenyedia(['status' => 'nonaktif']);
        $proyek = $this->makeProyek($desa);

        $result = $this->service->getRecommendations($proyek);

        $this->assertCount(0, $result);
    }

    /** Rule 1: wrong spesialisasi excluded */
    public function test_excludes_vendors_with_wrong_spesialisasi(): void
    {
        $desa = $this->makeDesa();
        $this->makePenyedia(['spesialisasi' => 'mikro_hidro']);
        $proyek = $this->makeProyek($desa, 'panel_surya');

        $result = $this->service->getRecommendations($proyek);

        $this->assertCount(0, $result);
    }

    /** Rule 3: insufficient capacity excluded */
    public function test_excludes_vendors_with_insufficient_capacity(): void
    {
        $desa = $this->makeDesa(['jumlah_penduduk' => 400]); // needs (400/4)*50 = 5000 kWh
        $this->makePenyedia(['kapasitas_maks' => 4999]);
        $proyek = $this->makeProyek($desa);

        $result = $this->service->getRecommendations($proyek);

        $this->assertCount(0, $result);
    }

    /** Rule 3: null kapasitas_maks passes through */
    public function test_null_kapasitas_passes_capacity_filter(): void
    {
        $desa = $this->makeDesa(['jumlah_penduduk' => 400]);
        $this->makePenyedia(['kapasitas_maks' => null]);
        $proyek = $this->makeProyek($desa);

        $result = $this->service->getRecommendations($proyek);

        $this->assertCount(1, $result);
    }

    /** Rule 3: null jumlah_penduduk skips capacity filter */
    public function test_null_jumlah_penduduk_skips_capacity_filter(): void
    {
        $desa = $this->makeDesa(['jumlah_penduduk' => null]);
        $this->makePenyedia(['kapasitas_maks' => 1]); // tiny capacity, would fail if rule ran
        $proyek = $this->makeProyek($desa);

        $result = $this->service->getRecommendations($proyek);

        $this->assertCount(1, $result);
    }

    /** Rule 2: closer vendor gets higher score */
    public function test_closer_vendor_gets_higher_score(): void
    {
        $desa = $this->makeDesa(['koordinat' => '-6.917464,107.619123']);
        // Near vendor (~2 km away)
        $this->makePenyedia(['nama' => 'Near', 'latitude' => -6.9, 'longitude' => 107.62]);
        // Far vendor (~300 km away)
        $this->makePenyedia(['nama' => 'Far', 'latitude' => -9.0, 'longitude' => 107.0]);
        $proyek = $this->makeProyek($desa);

        $result = $this->service->getRecommendations($proyek);

        $this->assertEquals('Near', $result->first()->nama);
        $this->assertGreaterThan($result->last()->match_score, $result->first()->match_score);
    }

    /** Rule 2: null koordinat gives score 50 */
    public function test_null_desa_koordinat_gives_neutral_score(): void
    {
        $desa = $this->makeDesa(['koordinat' => null]);
        $this->makePenyedia();
        $proyek = $this->makeProyek($desa);

        $result = $this->service->getRecommendations($proyek);

        $this->assertEquals(50, $result->first()->match_score);
        $this->assertNull($result->first()->distance_km);
    }

    /** Rule 2: null penyedia lat/lon gives score 50 */
    public function test_null_penyedia_coords_gives_neutral_score(): void
    {
        $desa = $this->makeDesa();
        $this->makePenyedia(['latitude' => null, 'longitude' => null]);
        $proyek = $this->makeProyek($desa);

        $result = $this->service->getRecommendations($proyek);

        $this->assertEquals(50, $result->first()->match_score);
    }

    /** Passing vendor has distance_km set */
    public function test_passing_vendor_has_distance_km(): void
    {
        $desa = $this->makeDesa(['koordinat' => '-6.917464,107.619123']);
        $this->makePenyedia(['latitude' => -6.917464, 'longitude' => 107.619123]);
        $proyek = $this->makeProyek($desa);

        $result = $this->service->getRecommendations($proyek);

        $this->assertNotNull($result->first()->distance_km);
        $this->assertEquals(0.0, $result->first()->distance_km);
        $this->assertEquals(100, $result->first()->match_score);
    }
}
