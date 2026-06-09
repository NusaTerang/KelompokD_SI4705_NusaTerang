<?php

namespace Tests\Feature;

use App\Models\Desa;
use App\Models\PenugasanProyek;
use App\Models\PenyediaEnergi;
use App\Models\ProgressProyekVendor;
use App\Models\Proyek;
use App\Models\User;
use App\Notifications\ProgressProyekDikirim;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PBI21_VendorProjectProgressTest extends TestCase
{
    use RefreshDatabase;

    // TC-01: Vendor berhasil submit update progres
    public function test_vendor_berhasil_submit_update_progres(): void
    {
        Notification::fake();

        [$vendorUser, $penugasan, $proyek] = $this->createAssignedExecutionProject();

        $response = $this->actingAs($vendorUser)
            ->post(route('vendor.proyek.progress.store', $penugasan->id_penugasan), [
                'persentase' => 50,
                'deskripsi' => 'Instalasi separuh selesai.',
                'status_progress' => 'berjalan',
            ]);

        $response->assertRedirect(route('vendor.proyek.progress.show', $penugasan->id_penugasan));

        $this->assertDatabaseHas('progress_proyek_vendor', [
            'id_penugasan' => $penugasan->id_penugasan,
            'persentase' => 50,
            'status_progress' => 'berjalan',
            'status' => 'submitted',
        ]);

        $publicResponse = $this->get(route('proyek.show', $proyek->id));
        $publicResponse->assertOk();
        $publicResponse->assertSee('50%');
        $publicResponse->assertSee('Instalasi separuh selesai.');

        Notification::assertSentTo(
            User::where('role', 'admin')->get(),
            ProgressProyekDikirim::class
        );
    }

    // TC-02: Validasi persentase di luar range
    public function test_validasi_persentase_di_luar_range(): void
    {
        [$vendorUser, $penugasan] = $this->createAssignedExecutionProject();

        // Test persentase > 100
        $response = $this->actingAs($vendorUser)
            ->from(route('vendor.proyek.progress.show', $penugasan->id_penugasan))
            ->post(route('vendor.proyek.progress.store', $penugasan->id_penugasan), [
                'persentase' => 110,
                'deskripsi' => 'Invalid percent.',
                'status_progress' => 'berjalan',
            ]);

        $response->assertSessionHasErrors('persentase');

        // Test persentase < 0
        $response = $this->actingAs($vendorUser)
            ->from(route('vendor.proyek.progress.show', $penugasan->id_penugasan))
            ->post(route('vendor.proyek.progress.store', $penugasan->id_penugasan), [
                'persentase' => -5,
                'deskripsi' => 'Persentase negatif tidak valid.',
                'status_progress' => 'berjalan',
            ]);

        $response->assertSessionHasErrors('persentase');
    }

    // TC-03: Pilih status selesai munculkan form laporan akhir
    public function test_pilih_status_selesai_munculkan_form_laporan_akhir(): void
    {
        [$vendorUser, $penugasan] = $this->createAssignedExecutionProject();

        $response = $this->actingAs($vendorUser)
            ->post(route('vendor.proyek.progress.store', $penugasan->id_penugasan), [
                'persentase' => 100,
                'deskripsi' => 'Semua komponen sudah terpasang dan diuji.',
                'status_progress' => 'selesai',
            ]);

        $response->assertRedirect(route('vendor.proyek.progress.show', $penugasan->id_penugasan));

        $this->assertDatabaseHas('progress_proyek_vendor', [
            'id_penugasan' => $penugasan->id_penugasan,
            'status_progress' => 'selesai',
            'status' => 'submitted',
        ]);

        $progressPageResponse = $this->actingAs($vendorUser)
            ->get(route('vendor.proyek.progress.show', $penugasan->id_penugasan));

        $progressPageResponse->assertSee('Laporan Akhir');
    }

    // TC-04: Vendor simpan draft
    public function test_vendor_simpan_draft(): void
    {
        [$vendorUser, $penugasan, $proyek] = $this->createAssignedExecutionProject();

        $response = $this->actingAs($vendorUser)
            ->post(route('vendor.proyek.progress.store', $penugasan->id_penugasan), [
                'save_draft' => '1',
                'persentase' => 30,
                'deskripsi' => 'Panel mulai dipasang.',
                'status_progress' => 'berjalan',
            ]);

        $response->assertRedirect(route('vendor.proyek.progress.show', $penugasan->id_penugasan));

        $this->assertDatabaseHas('progress_proyek_vendor', [
            'id_penugasan' => $penugasan->id_penugasan,
            'persentase' => 30,
            'deskripsi' => 'Panel mulai dipasang.',
            'status_progress' => 'berjalan',
            'status' => 'draft',
        ]);

        $publicResponse = $this->get(route('proyek.show', $proyek->id));
        $publicResponse->assertOk();
        $publicResponse->assertDontSee('Panel mulai dipasang.');
    }

    private function createAssignedExecutionProject(): array
    {
        $penyedia = PenyediaEnergi::create([
            'nama' => 'Vendor Progress Test',
            'spesialisasi' => 'panel_surya',
            'provinsi_operasi' => 'Jawa Barat',
            'kisaran_harga_min' => 1000000,
            'kisaran_harga_max' => 10000000,
            'status' => 'aktif',
        ]);

        $vendorUser = User::factory()->create([
            'role' => 'penyedia',
            'penyedia_id' => $penyedia->id,
        ]);

        $adminUser = User::factory()->create([
            'role' => 'admin',
        ]);

        $desa = Desa::create([
            'nama_desa' => 'Desa Progress Test',
            'provinsi' => 'Jawa Barat',
            'kabupaten' => 'Bandung',
            'kondisi_desa' => 'off-grid',
            'sumber' => 'solar_panel',
            'id_admin' => $adminUser->id,
        ]);

        $proyek = Proyek::create([
            'desa_id' => $desa->id_desa,
            'penyedia_id' => $penyedia->id,
            'judul' => 'Proyek Progress Test',
            'deskripsi' => 'Proyek untuk pengujian progress.',
            'jenis_energi' => 'panel_surya',
            'estimasi_mulai' => now()->subWeek()->toDateString(),
            'estimasi_selesai' => now()->addWeeks(4)->toDateString(),
            'target_dana' => 150000000,
            'dana_terkumpul' => 150000000,
            'status' => 'eksekusi',
            'created_by' => $adminUser->id,
        ]);

        $penugasan = PenugasanProyek::create([
            'id_proyek' => $proyek->id,
            'id_penyedia' => $penyedia->id,
            'status_penugasan' => 'diterima',
            'tanggal_respon' => now(),
        ]);

        return [$vendorUser, $penugasan, $proyek, $penyedia, $adminUser, $desa];
    }
}
