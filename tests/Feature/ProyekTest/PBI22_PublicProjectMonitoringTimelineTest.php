<?php

namespace Tests\Feature;

use App\Models\Desa;
use App\Models\PenugasanProyek;
use App\Models\LaporanAkhirProyekVendor;
use App\Models\PenyediaEnergi;
use App\Models\ProgressProyekVendor;
use App\Models\Proyek;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PBI22_PublicProjectMonitoringTimelineTest extends TestCase
{
    use RefreshDatabase;

    public function test_akses_monitoring_proyek_sedang_berjalan(): void
    {
        [$proyek, $penugasan] = $this->createProjectWithAssignment('eksekusi');

        ProgressProyekVendor::create([
            'id_penugasan' => $penugasan->id_penugasan,
            'persentase' => 40,
            'deskripsi' => 'Update lama.',
            'foto_paths' => null,
            'status_progress' => 'berjalan',
            'status' => 'submitted',
            'submitted_at' => now()->subDay(),
        ]);

        ProgressProyekVendor::create([
            'id_penugasan' => $penugasan->id_penugasan,
            'persentase' => 75,
            'deskripsi' => 'Panel surya hampir selesai.',
            'foto_paths' => ['progress/lapangan.jpg'],
            'status_progress' => 'berjalan',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $response = $this->get(route('proyek.show', $proyek->id));

        $response->assertOk();
        $response->assertSee('Sedang Berjalan');
        $response->assertSee('75%');
        $response->assertSee('style="width: 75%"', false);

        $html = $response->getContent();
        $this->assertLessThan(
            strpos($html, 'Update lama.'),
            strpos($html, 'Panel surya hampir selesai.')
        );

        $response->assertSee('storage/progress/lapangan.jpg', false);
    }

    public function test_akses_monitoring_proyek_selesai(): void
    {
        [$proyek, $penugasan] = $this->createProjectWithAssignment('selesai');

        ProgressProyekVendor::create([
            'id_penugasan' => $penugasan->id_penugasan,
            'persentase' => 100,
            'deskripsi' => 'Progress selesai 100%.',
            'foto_paths' => ['progress/selesai.jpg'],
            'status_progress' => 'selesai',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        LaporanAkhirProyekVendor::create([
            'id_penugasan' => $penugasan->id_penugasan,
            'id_proyek' => $proyek->id,
            'id_penyedia' => $penugasan->id_penyedia,
            'deskripsi' => 'Instalasi selesai dan sudah diuji bersama warga.',
            'kapasitas_terpasang' => 10,
            'satuan_kapasitas' => 'kWp',
            'foto_paths' => ['laporan/selesai.jpg'],
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $response = $this->get(route('proyek.show', $proyek->id));

        $response->assertOk();
        $response->assertSee('Selesai');
        $response->assertSee('Laporan Akhir');
        $response->assertSee('Instalasi selesai dan sudah diuji bersama warga.');
    }

    public function test_akses_monitoring_proyek_belum_ada_update(): void
    {
        [$proyek] = $this->createProjectWithAssignment('eksekusi');

        $response = $this->get(route('proyek.show', $proyek->id));

        $response->assertOk();
        $response->assertSee('Vendor belum mengunggah update progres');
    }

    public function test_proyek_tidak_ditemukan_returns_404(): void
    {
        $response = $this->get('/proyek/999999');

        $response->assertNotFound();
    }

    private function createProjectWithAssignment(string $status): array
    {
        $penyedia = PenyediaEnergi::create([
            'nama' => 'Vendor Monitoring Test',
            'spesialisasi' => 'panel_surya',
            'provinsi_operasi' => 'Jawa Barat',
            'kisaran_harga_min' => 1000000,
            'kisaran_harga_max' => 10000000,
            'status' => 'aktif',
        ]);

        $adminUser = User::factory()->create([
            'role' => 'admin',
        ]);

        $desa = Desa::create([
            'nama_desa' => 'Desa Monitoring',
            'provinsi' => 'Jawa Barat',
            'kabupaten' => 'Bandung',
            'kondisi_desa' => 'off-grid',
            'sumber' => 'solar_panel',
            'id_admin' => $adminUser->id,
        ]);

        $proyek = Proyek::create([
            'desa_id' => $desa->id_desa,
            'penyedia_id' => $penyedia->id,
            'judul' => 'Proyek Monitoring Publik',
            'deskripsi' => 'Kebutuhan energi desa untuk monitoring publik.',
            'jenis_energi' => 'panel_surya',
            'estimasi_mulai' => now()->subWeek()->toDateString(),
            'estimasi_selesai' => now()->addWeeks(4)->toDateString(),
            'target_dana' => 150000000,
            'dana_terkumpul' => 100000000,
            'status' => $status,
            'created_by' => $adminUser->id,
        ]);

        $penugasan = PenugasanProyek::create([
            'id_proyek' => $proyek->id,
            'id_penyedia' => $penyedia->id,
            'status_penugasan' => 'diterima',
            'tanggal_respon' => now(),
        ]);

        return [$proyek, $penugasan, $penyedia, $desa];
    }
}
