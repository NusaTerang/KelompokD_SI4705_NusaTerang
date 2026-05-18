<?php

namespace Tests\Feature;

use App\Models\Desa;
use App\Models\Proyek;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageWilayahFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_wilayah_filter_uses_active_project_provinces(): void
    {
        $jawaBarat = Desa::create([
            'nama_desa' => 'Desa Cikaret',
            'provinsi' => 'Jawa Barat',
            'kabupaten' => 'Bogor',
            'sumber' => 'solar_panel',
            'status_verifikasi' => 'terverifikasi',
        ]);
        $jawaTengah = Desa::create([
            'nama_desa' => 'Desa Sumber Makmur',
            'provinsi' => 'Jawa Tengah',
            'kabupaten' => 'Banjarnegara',
            'sumber' => 'mikro_hidro',
            'status_verifikasi' => 'terverifikasi',
        ]);
        $papua = Desa::create([
            'nama_desa' => 'Desa Papua Nonaktif',
            'provinsi' => 'Papua',
            'kabupaten' => 'Merauke',
            'sumber' => 'biomassa',
            'status_verifikasi' => 'terverifikasi',
        ]);

        Proyek::create([
            'desa_id' => $jawaBarat->id_desa,
            'judul' => 'PLTS Jawa Barat',
            'deskripsi' => 'Proyek aktif Jawa Barat',
            'target_dana' => 1000000,
            'dana_terkumpul' => 250000,
            'status' => 'aktif_funding',
        ]);
        Proyek::create([
            'desa_id' => $jawaTengah->id_desa,
            'judul' => 'Mikrohidro Jawa Tengah',
            'deskripsi' => 'Proyek aktif Jawa Tengah',
            'target_dana' => 1000000,
            'dana_terkumpul' => 500000,
            'status' => 'aktif_funding',
        ]);
        Proyek::create([
            'desa_id' => $papua->id_desa,
            'judul' => 'Proyek Papua Selesai',
            'deskripsi' => 'Proyek selesai Papua',
            'target_dana' => 1000000,
            'dana_terkumpul' => 1000000,
            'status' => 'selesai',
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('value="Jawa Barat"', false);
        $response->assertSee('value="Jawa Tengah"', false);
        $response->assertDontSee('value="Papua"', false);
        $response->assertDontSee('value="Nusa Tenggara Timur"', false);
    }

    public function test_homepage_filters_projects_by_selected_wilayah(): void
    {
        $jawaBarat = Desa::create([
            'nama_desa' => 'Desa Cikaret',
            'provinsi' => 'Jawa Barat',
            'kabupaten' => 'Bogor',
            'sumber' => 'solar_panel',
            'status_verifikasi' => 'terverifikasi',
        ]);
        $jawaTengah = Desa::create([
            'nama_desa' => 'Desa Sumber Makmur',
            'provinsi' => 'Jawa Tengah',
            'kabupaten' => 'Banjarnegara',
            'sumber' => 'mikro_hidro',
            'status_verifikasi' => 'terverifikasi',
        ]);

        Proyek::create([
            'desa_id' => $jawaBarat->id_desa,
            'judul' => 'PLTS Jawa Barat',
            'deskripsi' => 'Proyek aktif Jawa Barat',
            'target_dana' => 1000000,
            'dana_terkumpul' => 250000,
            'status' => 'aktif_funding',
        ]);
        Proyek::create([
            'desa_id' => $jawaTengah->id_desa,
            'judul' => 'Mikrohidro Jawa Tengah',
            'deskripsi' => 'Proyek aktif Jawa Tengah',
            'target_dana' => 1000000,
            'dana_terkumpul' => 500000,
            'status' => 'aktif_funding',
        ]);

        $response = $this->get('/?wilayah=Jawa%20Barat');

        $response->assertOk();
        $response->assertSee('PLTS Jawa Barat');
        $response->assertDontSee('Mikrohidro Jawa Tengah');
        $response->assertSee('value="Jawa Barat" selected', false);
    }
}
