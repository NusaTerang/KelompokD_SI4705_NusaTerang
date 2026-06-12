<?php

namespace Tests\Browser\ProyekTest\Concerns;

use App\Models\Desa;
use App\Models\Donasi;
use App\Models\LaporanAkhirProyekVendor;
use App\Models\PenugasanProyek;
use App\Models\PenyediaEnergi;
use App\Models\ProgressProyekVendor;
use App\Models\Proyek;
use App\Models\User;
use Laravel\Dusk\Browser;

trait CreatesProyekFixtures
{
    protected function createPenyedia(array $overrides = []): PenyediaEnergi
    {
        return PenyediaEnergi::create(array_merge([
            'nama' => 'Vendor Dusk Test',
            'spesialisasi' => 'panel_surya',
            'provinsi_operasi' => 'Jawa Barat',
            'kisaran_harga_min' => 1000000,
            'kisaran_harga_max' => 10000000,
            'status' => 'aktif',
        ], $overrides));
    }

    protected function createAdmin(array $overrides = []): User
    {
        return User::factory()->create(array_merge(['role' => 'admin'], $overrides));
    }

    protected function createDonatur(array $overrides = []): User
    {
        return User::factory()->create(array_merge(['role' => 'donatur'], $overrides));
    }

    protected function createVendorUser(?PenyediaEnergi $penyedia = null, array $overrides = []): User
    {
        $penyedia ??= $this->createPenyedia();

        return User::factory()->create(array_merge([
            'role' => 'penyedia',
            'penyedia_id' => $penyedia->id,
        ], $overrides));
    }

    protected function createDesa(?User $admin = null, array $overrides = []): Desa
    {
        $admin ??= $this->createAdmin();

        return Desa::create(array_merge([
            'nama_desa' => 'Desa Dusk Test',
            'provinsi' => 'Jawa Barat',
            'kabupaten' => 'Bandung',
            'kondisi_desa' => 'off-grid',
            'sumber' => 'solar_panel',
            'id_admin' => $admin->getKey(),
        ], $overrides));
    }

    protected function createProject(array $overrides = []): Proyek
    {
        $admin = $overrides['admin'] ?? $this->createAdmin();
        unset($overrides['admin']);

        $desa = $overrides['desa'] ?? $this->createDesa($admin);
        unset($overrides['desa']);

        return Proyek::create(array_merge([
            'desa_id' => $desa->id_desa,
            'judul' => 'Proyek Dusk Test',
            'deskripsi' => 'Proyek untuk pengujian Dusk.',
            'jenis_energi' => 'panel_surya',
            'estimasi_mulai' => now()->subWeek()->toDateString(),
            'estimasi_selesai' => now()->addWeeks(4)->toDateString(),
            'target_dana' => 150000000,
            'dana_terkumpul' => 0,
            'status' => 'draft',
            'created_by' => $admin->getKey(),
        ], $overrides));
    }

    protected function createAssignedExecutionProject(): array
    {
        $penyedia = $this->createPenyedia(['nama' => 'Vendor Progress Dusk']);
        $vendorUser = $this->createVendorUser($penyedia);
        $admin = $this->createAdmin();
        $desa = $this->createDesa($admin, ['nama_desa' => 'Desa Progress Dusk']);
        $proyek = $this->createProject([
            'admin' => $admin,
            'desa' => $desa,
            'penyedia_id' => $penyedia->id,
            'judul' => 'Proyek Progress Dusk',
            'deskripsi' => 'Proyek untuk update progress Dusk.',
            'target_dana' => 150000000,
            'dana_terkumpul' => 150000000,
            'status' => 'eksekusi',
        ]);

        $penugasan = PenugasanProyek::create([
            'id_proyek' => $proyek->id,
            'id_penyedia' => $penyedia->id,
            'status_penugasan' => 'diterima',
            'tanggal_respon' => now(),
        ]);

        return [$vendorUser, $penugasan, $proyek, $penyedia, $admin, $desa];
    }

    protected function createCompletedProgressProject(): array
    {
        [$vendorUser, $penugasan, $proyek, $penyedia, $admin, $desa] = $this->createAssignedExecutionProject();

        ProgressProyekVendor::create([
            'id_penugasan' => $penugasan->id_penugasan,
            'persentase' => 100,
            'deskripsi' => 'Instalasi sudah 100% selesai.',
            'status_progress' => 'selesai',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return [$vendorUser, $penugasan, $proyek, $penyedia, $admin, $desa];
    }

    protected function createProjectWithAssignment(string $status = 'eksekusi'): array
    {
        $penyedia = $this->createPenyedia(['nama' => 'Vendor Monitoring Dusk']);
        $admin = $this->createAdmin();
        $desa = $this->createDesa($admin, ['nama_desa' => 'Desa Monitoring Dusk']);
        $proyek = $this->createProject([
            'admin' => $admin,
            'desa' => $desa,
            'penyedia_id' => $penyedia->id,
            'judul' => 'Proyek Monitoring Dusk',
            'deskripsi' => 'Kebutuhan energi desa untuk monitoring publik.',
            'target_dana' => 150000000,
            'dana_terkumpul' => 100000000,
            'status' => $status,
        ]);

        $penugasan = PenugasanProyek::create([
            'id_proyek' => $proyek->id,
            'id_penyedia' => $penyedia->id,
            'status_penugasan' => 'diterima',
            'tanggal_respon' => now(),
        ]);

        return [$proyek, $penugasan, $penyedia, $desa];
    }

    protected function createExpiredVendorProject(string $status = 'menunggu_keputusan_vendor'): array
    {
        $penyedia = $this->createPenyedia(['nama' => 'Vendor Expired Dusk']);
        $vendor = $this->createVendorUser($penyedia);
        $proyek = $this->createProject([
            'penyedia_id' => $penyedia->id,
            'judul' => 'Vendor Decision Dusk',
            'deskripsi' => 'Need decision.',
            'estimasi_mulai' => now()->subMonth()->toDateString(),
            'estimasi_selesai' => now()->addDays(30)->toDateString(),
            'status' => $status,
            'dana_terkumpul' => 7500000,
            'expired_extension_pending' => true,
            'expired_original_end_date' => now()->subDay()->toDateString(),
            'expired_extended_at' => now(),
        ]);
        $penugasan = PenugasanProyek::create([
            'id_proyek' => $proyek->id,
            'id_penyedia' => $penyedia->id,
            'status_penugasan' => 'diterima',
        ]);

        return [$vendor, $proyek, $penugasan, $penyedia];
    }

    protected function createCancelledProject(int $donorCount = 3): array
    {
        $admin = $this->createAdmin();
        $proyek = $this->createProject([
            'admin' => $admin,
            'judul' => 'Proyek Refund Dusk',
            'deskripsi' => 'Proyek untuk refund.',
            'estimasi_mulai' => now()->subWeeks(8)->toDateString(),
            'estimasi_selesai' => now()->subDay()->toDateString(),
            'target_dana' => 500000,
            'dana_terkumpul' => 300000,
            'status' => 'aktif_funding',
        ]);

        $donors = [];
        for ($i = 0; $i < $donorCount; $i++) {
            $donors[] = $this->createDonatur();
        }

        return [$proyek, $donors, $admin];
    }

    protected function addSuccessfulDonation(Proyek $proyek, User $donor, int $nominal): Donasi
    {
        return Donasi::create([
            'id_proyek' => $proyek->id,
            'id_donatur' => $donor->getKey(),
            'nominal' => $nominal,
            'status' => 'success',
        ]);
    }

    protected function imageFixturePath(): string
    {
        return base_path('src/assets/hero.png');
    }

    protected function chooseRadio(Browser $browser, string $name, string $value): void
    {
        $browser->script(
            "const input = document.querySelector('input[name=\"{$name}\"][value=\"{$value}\"]');"
            . "input.checked = true;"
            . "input.dispatchEvent(new Event('change', { bubbles: true }));"
        );
    }
}
