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

class VendorFinalReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_vendor_can_save_final_report_draft_without_completing_project(): void
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

    public function test_vendor_can_submit_final_report_and_complete_project(): void
    {
        Storage::fake('public');
        Notification::fake();

        [$vendorUser, $penugasan, $proyek, , $adminUser] = $this->createProjectWithCompletedProgress();

        $response = $this->actingAs($vendorUser)
            ->post(route('vendor.proyek.final-report.store', $penugasan->id_penugasan), [
                'deskripsi' => 'Seluruh instalasi panel surya selesai dan berfungsi.',
                'kapasitas_terpasang' => 15,
                'satuan_kapasitas' => 'kWp',
                'catatan' => 'Sistem siap digunakan warga.',
                'fotos' => [UploadedFile::fake()->image('laporan-final.jpg')],
            ]);

        $response->assertRedirect(route('vendor.proyek.show', $penugasan->id_penugasan));

        $this->assertDatabaseHas('laporan_akhir_proyek_vendor', [
            'id_penugasan' => $penugasan->id_penugasan,
            'id_proyek' => $proyek->id,
            'deskripsi' => 'Seluruh instalasi panel surya selesai dan berfungsi.',
            'kapasitas_terpasang' => 15,
            'satuan_kapasitas' => 'kWp',
            'status' => 'submitted',
        ]);

        $this->assertDatabaseHas('proyeks', [
            'id' => $proyek->id,
            'status' => 'selesai',
        ]);

        Notification::assertSentTo($adminUser, ProyekSelesai::class);
    }

    public function test_submitted_final_report_requires_description_and_photo(): void
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

    public function test_vendor_cannot_submit_final_report_twice(): void
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

    public function test_other_vendor_cannot_submit_final_report_for_unowned_project(): void
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
    }

    public function test_final_report_is_visible_on_public_project_detail(): void
    {
        Storage::fake('public');

        [$vendorUser, $penugasan, $proyek] = $this->createProjectWithCompletedProgress();

        $this->actingAs($vendorUser)
            ->post(route('vendor.proyek.final-report.store', $penugasan->id_penugasan), [
                'deskripsi' => 'Laporan publik terlihat di detail proyek.',
                'kapasitas_terpasang' => 20,
                'satuan_kapasitas' => 'kWp',
                'catatan' => 'Dokumentasi lengkap.',
                'fotos' => [UploadedFile::fake()->image('laporan-public.jpg')],
            ]);

        $response = $this->get(route('proyek.show', $proyek->id));

        $response->assertOk();
        $response->assertSee('Laporan Akhir');
        $response->assertSee('Laporan publik terlihat di detail proyek.');
        $response->assertSee('20 kWp');
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
