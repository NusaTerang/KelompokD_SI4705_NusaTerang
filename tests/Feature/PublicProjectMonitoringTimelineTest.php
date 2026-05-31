<?php

namespace Tests\Feature;

use App\Models\Desa;
use App\Models\PenugasanProyek;
use App\Models\PenyediaEnergi;
use App\Models\ProgressProyekVendor;
use App\Models\Proyek;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicProjectMonitoringTimelineTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_project_detail_shows_empty_monitoring_state_without_login(): void
    {
        [$proyek] = $this->createProjectWithAssignment('eksekusi');

        $response = $this->get(route('proyek.show', $proyek->id));

        $response->assertOk();
        $response->assertSee('Timeline', false);
        $response->assertSee('Progress Updates', false);
        $response->assertSee('Vendor belum mengunggah update progres');
    }

    public function test_project_execution_status_uses_public_monitoring_label(): void
    {
        [$proyek] = $this->createProjectWithAssignment('eksekusi');

        $response = $this->get(route('proyek.show', $proyek->id));

        $response->assertOk();
        $response->assertSee('Sedang Berjalan');
    }

    public function test_completed_project_shows_final_report_section(): void
    {
        [$proyek, $penugasan] = $this->createProjectWithAssignment('selesai');

        ProgressProyekVendor::create([
            'id_penugasan' => $penugasan->id_penugasan,
            'persentase' => 100,
            'deskripsi' => 'Instalasi selesai dan sudah diuji bersama warga.',
            'foto_paths' => ['progress/selesai.jpg'],
            'status_progress' => 'selesai',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $response = $this->get(route('proyek.show', $proyek->id));

        $response->assertOk();
        $response->assertSee('Selesai');
        $response->assertSee('Laporan Akhir');
        $response->assertSee('Instalasi selesai dan sudah diuji bersama warga.');
    }

    public function test_progress_updates_render_newest_first(): void
    {
        [$proyek, $penugasan] = $this->createProjectWithAssignment('eksekusi');

        ProgressProyekVendor::create([
            'id_penugasan' => $penugasan->id_penugasan,
            'persentase' => 35,
            'deskripsi' => 'Pemasangan pondasi dimulai.',
            'status_progress' => 'berjalan',
            'status' => 'submitted',
            'submitted_at' => now()->subDays(2),
        ]);

        ProgressProyekVendor::create([
            'id_penugasan' => $penugasan->id_penugasan,
            'persentase' => 75,
            'deskripsi' => 'Panel surya sudah terpasang sebagian besar.',
            'status_progress' => 'berjalan',
            'status' => 'submitted',
            'submitted_at' => now()->subDay(),
        ]);

        $response = $this->get(route('proyek.show', $proyek->id));

        $response->assertOk();

        $html = $response->getContent();
        $this->assertLessThan(
            strpos($html, 'Pemasangan pondasi dimulai.'),
            strpos($html, 'Panel surya sudah terpasang sebagian besar.')
        );
    }

    public function test_latest_submitted_update_percentage_drives_monitoring_progress_bar(): void
    {
        [$proyek, $penugasan] = $this->createProjectWithAssignment('eksekusi');

        ProgressProyekVendor::create([
            'id_penugasan' => $penugasan->id_penugasan,
            'persentase' => 40,
            'deskripsi' => 'Update lama.',
            'status_progress' => 'berjalan',
            'status' => 'submitted',
            'submitted_at' => now()->subDays(3),
        ]);

        ProgressProyekVendor::create([
            'id_penugasan' => $penugasan->id_penugasan,
            'persentase' => 75,
            'deskripsi' => 'Update terbaru.',
            'status_progress' => 'berjalan',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $response = $this->get(route('proyek.show', $proyek->id));

        $response->assertOk();
        $response->assertSee('Progress Pengerjaan');
        $response->assertSee('75%');
        $response->assertSee('style="width: 75%"', false);
    }

    public function test_timeline_renders_progress_photos(): void
    {
        [$proyek, $penugasan] = $this->createProjectWithAssignment('eksekusi');

        ProgressProyekVendor::create([
            'id_penugasan' => $penugasan->id_penugasan,
            'persentase' => 60,
            'deskripsi' => 'Foto lapangan tersedia.',
            'foto_paths' => ['progress/foto-1.jpg', 'https://example.test/foto-2.jpg'],
            'status_progress' => 'berjalan',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $response = $this->get(route('proyek.show', $proyek->id));

        $response->assertOk();
        $response->assertSee('storage/progress/foto-1.jpg', false);
        $response->assertSee('https://example.test/foto-2.jpg', false);
    }

    public function test_missing_project_returns_not_found(): void
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
