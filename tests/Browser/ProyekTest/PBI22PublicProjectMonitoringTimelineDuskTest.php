<?php

namespace Tests\Browser\ProyekTest;

use App\Models\LaporanAkhirProyekVendor;
use App\Models\ProgressProyekVendor;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\Browser\ProyekTest\Concerns\CreatesProyekFixtures;
use Tests\DuskTestCase;

class PBI22PublicProjectMonitoringTimelineDuskTest extends DuskTestCase
{
    use CreatesProyekFixtures;
    use DatabaseTruncation;

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

        $this->browse(function (Browser $browser) use ($proyek) {
            $browser->visit("/projects/{$proyek->id}/monitoring")
                ->assertSee('Sedang Berjalan')
                ->assertSee('75%')
                ->assertSee('Panel surya hampir selesai.')
                ->assertSee('Update lama.');
        });
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

        $this->browse(function (Browser $browser) use ($proyek) {
            $browser->visit("/projects/{$proyek->id}/monitoring")
                ->assertSee('Selesai')
                ->assertSee('100%')
                ->assertSee('Laporan Akhir')
                ->assertSee('Instalasi selesai dan sudah diuji bersama warga.');
        });
    }

    public function test_akses_monitoring_proyek_belum_ada_update(): void
    {
        [$proyek] = $this->createProjectWithAssignment('eksekusi');

        $this->browse(function (Browser $browser) use ($proyek) {
            $browser->visit("/projects/{$proyek->id}/monitoring")
                ->assertSee('Vendor belum mengunggah update progres');
        });
    }

    public function test_proyek_tidak_ditemukan_returns_404(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/projects/999999/monitoring')
                ->assertSee('404');
        });
    }
}
