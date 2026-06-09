<?php

namespace Tests\Feature;

use App\Models\Desa;
use App\Models\PenugasanProyek;
use App\Models\PenyediaEnergi;
use App\Models\ProgressProyekVendor;
use App\Models\Proyek;
use App\Models\User;
use App\Notifications\ProyekSelesai;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PBI25_VendorFinalReportTest extends TestCase
{
    use RefreshDatabase;

    // TC-01
    public function test_vendor_berhasil_submit_laporan_akhir(): void
    {
        Storage::fake('public');
        Notification::fake();

        [$vendorUser, $penugasan, $proyek, , $adminUser] = $this->createProjectWithCompletedProgress();

        $response = $this->actingAs($vendorUser)
            ->post(route('vendor.proyek.final-report.store', $penugasan->id_penugasan), [
                'deskripsi' => 'Seluruh instalasi panel surya selesai.',
                'kapasitas_terpasang' => 15,
                'satuan_kapasitas' => 'kWp',
                'catatan' => 'Sistem siap digunakan warga.',
                'fotos' => [UploadedFile::fake()->image('laporan-final.jpg')],
            ]);

        $response->assertRedirect(route('vendor.proyek.show', $penugasan->id_penugasan));

        $this->assertDatabaseHas('laporan_akhir_proyek_vendor', [
            'id_penugasan' => $penugasan->id_penugasan,
            'id_proyek' => $proyek->id,
            'deskripsi' => 'Seluruh instalasi panel surya selesai.',
            'kapasitas_terpasang' => 15,
            'satuan_kapasitas' => 'kWp',
            'status' => 'submitted',
        ]);

        $this->assertDatabaseHas('proyeks', [
            'id' => $proyek->id,
            'status' => 'selesai',
        ]);

        Notification::assertSentTo($adminUser, ProyekSelesai::class);

        $publicResponse = $this->get(route('proyek.show', $proyek->id));
        $publicResponse->assertSee('Laporan Akhir');
        $publicResponse->assertSee('Seluruh instalasi panel surya selesai.');
    }

    // TC-02
    public function test_vendor_simpan_draft_laporan_akhir(): void
    {
        Storage::fake('public');

        [$vendorUser, $penugasan, $proyek] = $this->createProjectWithCompletedProgress();

        $response = $this->actingAs($vendorUser)
            ->post(route('vendor.proyek.final-report.store', $penugasan->id_penugasan), [
                'save_draft' => '1',
                'deskripsi' => 'Instalasi selesai dan perangkat sudah diuji.',
                'kapasitas_terpasang' => 12.5,
                'satuan_kapasitas' => 'kWp',
                'catatan' => 'Menunggu pengecekan akhir admin.',
                'fotos' => [UploadedFile::fake()->image('laporan-draft.jpg')],
            ]);

        $response->assertRedirect(route('vendor.proyek.show', $penugasan->id_penugasan));

        $this->assertDatabaseHas('laporan_akhir_proyek_vendor', [
            'id_penugasan' => $penugasan->id_penugasan,
            'deskripsi' => 'Instalasi selesai dan perangkat sudah diuji.',
            'kapasitas_terpasang' => 12.5,
            'satuan_kapasitas' => 'kWp',
            'status' => 'draft',
        ]);

        $this->assertDatabaseHas('proyeks', [
            'id' => $proyek->id,
            'status' => 'eksekusi',
        ]);
    }

    // TC-03
    public function test_validasi_field_wajib(): void
    {
        [$vendorUser, $penugasan] = $this->createProjectWithCompletedProgress();

        $response = $this->actingAs($vendorUser)
            ->from(route('vendor.proyek.show', $penugasan->id_penugasan))
            ->post(route('vendor.proyek.final-report.store', $penugasan->id_penugasan), [
                'kapasitas_terpasang' => 15,
                'satuan_kapasitas' => 'kWp',
            ]);

        $response->assertRedirect(route('vendor.proyek.show', $penugasan->id_penugasan));
        $response->assertSessionHasErrors(['deskripsi', 'fotos']);
    }

    // TC-04
    public function test_validasi_pencegahan_duplikasi_laporan(): void
    {
        Storage::fake('public');

        [$vendorUser, $penugasan] = $this->createProjectWithCompletedProgress();

        $payload = [
            'deskripsi' => 'Laporan pertama.',
            'kapasitas_terpasang' => 10,
            'satuan_kapasitas' => 'kWp',
            'fotos' => [UploadedFile::fake()->image('laporan-1.jpg')],
        ];

        $this->actingAs($vendorUser)
            ->post(route('vendor.proyek.final-report.store', $penugasan->id_penugasan), $payload);

        $response = $this->actingAs($vendorUser)
            ->from(route('vendor.proyek.show', $penugasan->id_penugasan))
            ->post(route('vendor.proyek.final-report.store', $penugasan->id_penugasan), [
                'deskripsi' => 'Laporan kedua.',
                'kapasitas_terpasang' => 11,
                'satuan_kapasitas' => 'kWp',
                'fotos' => [UploadedFile::fake()->image('laporan-2.jpg')],
            ]);

        $response->assertRedirect(route('vendor.proyek.show', $penugasan->id_penugasan));
        $response->assertSessionHasErrors('laporan_akhir');
    }

    // TC-05
    public function test_validasi_hak_akses(): void
    {
        [, $penugasan] = $this->createProjectWithCompletedProgress();
        [$otherVendorUser] = $this->createOtherVendor();

        $response = $this->actingAs($otherVendorUser)
            ->post(route('vendor.proyek.final-report.store', $penugasan->id_penugasan), [
                'deskripsi' => 'Bukan proyek saya.',
                'kapasitas_terpasang' => 10,
                'satuan_kapasitas' => 'kWp',
            ]);

        $response->assertForbidden();

        $adminUser = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($adminUser)
            ->post(route('vendor.proyek.final-report.store', $penugasan->id_penugasan), [
                'deskripsi' => 'Admin mencoba submit laporan.',
                'kapasitas_terpasang' => 10,
                'satuan_kapasitas' => 'kWp',
            ]);

        $response->assertForbidden();
    }

    private function createProjectWithCompletedProgress(): array
    {
        [$vendorUser, $penugasan, $proyek, $penyedia, $adminUser, $desa] = $this->createAssignedExecutionProject();

        ProgressProyekVendor::create([
            'id_penugasan' => $penugasan->id_penugasan,
            'persentase' => 100,
            'deskripsi' => 'Instalasi sudah 100% selesai.',
            'status_progress' => 'selesai',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return [$vendorUser, $penugasan, $proyek, $penyedia, $adminUser, $desa];
    }

    private function createAssignedExecutionProject(): array
    {
        $penyedia = PenyediaEnergi::create([
            'nama' => 'Vendor Laporan Akhir Test',
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
            'nama_desa' => 'Desa Laporan Akhir Test',
            'provinsi' => 'Jawa Barat',
            'kabupaten' => 'Bandung',
            'kondisi_desa' => 'off-grid',
            'sumber' => 'solar_panel',
            'id_admin' => $adminUser->id,
        ]);

        $proyek = Proyek::create([
            'desa_id' => $desa->id_desa,
            'penyedia_id' => $penyedia->id,
            'judul' => 'Proyek Laporan Akhir Test',
            'deskripsi' => 'Proyek untuk pengujian laporan akhir.',
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

    private function createOtherVendor(): array
    {
        $penyedia = PenyediaEnergi::create([
            'nama' => 'Vendor Laporan Lain',
            'spesialisasi' => 'biogas',
            'provinsi_operasi' => 'Jawa Barat',
            'kisaran_harga_min' => 1000000,
            'kisaran_harga_max' => 10000000,
            'status' => 'aktif',
        ]);

        $user = User::factory()->create([
            'role' => 'penyedia',
            'penyedia_id' => $penyedia->id,
        ]);

        return [$user, $penyedia];
    }
}
