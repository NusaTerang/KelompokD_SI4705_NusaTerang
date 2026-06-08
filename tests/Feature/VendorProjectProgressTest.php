<?php

namespace Tests\Feature;

use App\Models\Desa;
use App\Models\PenugasanProyek;
use App\Models\PenyediaEnergi;
use App\Models\ProgressProyekVendor;
use App\Models\Proyek;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VendorProjectProgressTest extends TestCase
{
    use RefreshDatabase;

    public function test_assigned_vendor_can_open_progress_page(): void
    {
        [$vendorUser, $penugasan] = $this->createAssignedExecutionProject();

        $response = $this->actingAs($vendorUser)
            ->get(route('vendor.proyek.progress.show', $penugasan->id_penugasan));

        $response->assertOk();
        $response->assertSee('Update Progress');
        $response->assertSee('Persentase Penyelesaian');
    }

    public function test_other_vendor_gets_forbidden_opening_progress_page(): void
    {
        [, $penugasan] = $this->createAssignedExecutionProject();
        [$otherUser] = $this->createOtherVendor();

        $response = $this->actingAs($otherUser)
            ->get(route('vendor.proyek.progress.show', $penugasan->id_penugasan));

        $response->assertForbidden();
    }

    public function test_vendor_can_save_progress_draft_without_public_visibility(): void
    {
        [$vendorUser, $penugasan, $proyek] = $this->createAssignedExecutionProject();

        $response = $this->actingAs($vendorUser)
            ->post(route('vendor.proyek.progress.store', $penugasan->id_penugasan), [
                'save_draft' => '1',
                'persentase' => 45,
                'deskripsi' => 'Panel mulai dipasang.',
                'status_progress' => 'berjalan',
            ]);

        $response->assertRedirect(route('vendor.proyek.progress.show', $penugasan->id_penugasan));

        $this->assertDatabaseHas('progress_proyek_vendor', [
            'id_penugasan' => $penugasan->id_penugasan,
            'persentase' => 45,
            'deskripsi' => 'Panel mulai dipasang.',
            'status_progress' => 'berjalan',
            'status' => 'draft',
        ]);

        $publicResponse = $this->get(route('proyek.show', $proyek->id));
        $publicResponse->assertOk();
        $publicResponse->assertDontSee('Panel mulai dipasang.');
    }

    public function test_vendor_can_submit_progress_update_and_it_is_public(): void
    {
        [$vendorUser, $penugasan, $proyek] = $this->createAssignedExecutionProject();

        $response = $this->actingAs($vendorUser)
            ->post(route('vendor.proyek.progress.store', $penugasan->id_penugasan), [
                'persentase' => 55,
                'deskripsi' => 'Instalasi rangka dan panel selesai separuh.',
                'status_progress' => 'berjalan',
            ]);

        $response->assertRedirect(route('vendor.proyek.progress.show', $penugasan->id_penugasan));

        $this->assertDatabaseHas('progress_proyek_vendor', [
            'id_penugasan' => $penugasan->id_penugasan,
            'persentase' => 55,
            'deskripsi' => 'Instalasi rangka dan panel selesai separuh.',
            'status_progress' => 'berjalan',
            'status' => 'submitted',
        ]);

        $publicResponse = $this->get(route('proyek.show', $proyek->id));
        $publicResponse->assertOk();
        $publicResponse->assertSee('Instalasi rangka dan panel selesai separuh.');
        $publicResponse->assertSee('55%');
    }

    public function test_submitted_progress_requires_percentage_between_zero_and_one_hundred(): void
    {
        [$vendorUser, $penugasan] = $this->createAssignedExecutionProject();

        $response = $this->actingAs($vendorUser)
            ->from(route('vendor.proyek.progress.show', $penugasan->id_penugasan))
            ->post(route('vendor.proyek.progress.store', $penugasan->id_penugasan), [
                'persentase' => 101,
                'deskripsi' => 'Invalid percent.',
                'status_progress' => 'berjalan',
            ]);

        $response->assertRedirect(route('vendor.proyek.progress.show', $penugasan->id_penugasan));
        $response->assertSessionHasErrors('persentase');
    }

    public function test_vendor_can_submit_selesai_progress_without_completing_project_before_final_report(): void
    {
        [$vendorUser, $penugasan, $proyek] = $this->createAssignedExecutionProject();

        $response = $this->actingAs($vendorUser)
            ->post(route('vendor.proyek.progress.store', $penugasan->id_penugasan), [
                'persentase' => 100,
                'deskripsi' => 'Semua komponen sudah terpasang dan diuji.',
                'status_progress' => 'selesai',
            ]);

        $response->assertRedirect(route('vendor.proyek.progress.show', $penugasan->id_penugasan));

        $this->assertDatabaseHas('proyeks', [
            'id' => $proyek->id,
            'status' => 'eksekusi',
        ]);

        $this->assertDatabaseHas('progress_proyek_vendor', [
            'id_penugasan' => $penugasan->id_penugasan,
            'status_progress' => 'selesai',
            'status' => 'submitted',
        ]);
    }

    public function test_vendor_can_submit_dijadwalkan_status(): void
    {
        [$vendorUser, $penugasan] = $this->createAssignedExecutionProject();

        $response = $this->actingAs($vendorUser)
            ->post(route('vendor.proyek.progress.store', $penugasan->id_penugasan), [
                'persentase' => 10,
                'deskripsi' => 'Jadwal mobilisasi tim sudah disusun.',
                'status_progress' => 'dijadwalkan',
            ]);

        $response->assertRedirect(route('vendor.proyek.progress.show', $penugasan->id_penugasan));

        $this->assertDatabaseHas('progress_proyek_vendor', [
            'id_penugasan' => $penugasan->id_penugasan,
            'status_progress' => 'dijadwalkan',
            'status' => 'submitted',
        ]);
    }

    public function test_progress_update_rejects_more_than_five_photos(): void
    {
        Storage::fake('public');

        [$vendorUser, $penugasan] = $this->createAssignedExecutionProject();

        $files = [];
        for ($i = 0; $i < 6; $i++) {
            $files[] = UploadedFile::fake()->image("progress-{$i}.jpg");
        }

        $response = $this->actingAs($vendorUser)
            ->from(route('vendor.proyek.progress.show', $penugasan->id_penugasan))
            ->post(route('vendor.proyek.progress.store', $penugasan->id_penugasan), [
                'persentase' => 30,
                'deskripsi' => 'Dokumentasi lapangan.',
                'status_progress' => 'berjalan',
                'fotos' => $files,
            ]);

        $response->assertRedirect(route('vendor.proyek.progress.show', $penugasan->id_penugasan));
        $response->assertSessionHasErrors('fotos');
    }

    public function test_submitted_progress_stores_photos_on_public_disk(): void
    {
        Storage::fake('public');

        [$vendorUser, $penugasan] = $this->createAssignedExecutionProject();

        $response = $this->actingAs($vendorUser)
            ->post(route('vendor.proyek.progress.store', $penugasan->id_penugasan), [
                'persentase' => 25,
                'deskripsi' => 'Dokumentasi pemasangan tiang.',
                'status_progress' => 'berjalan',
                'fotos' => [
                    UploadedFile::fake()->image('progress-1.jpg'),
                    UploadedFile::fake()->image('progress-2.jpg'),
                ],
            ]);

        $response->assertRedirect(route('vendor.proyek.progress.show', $penugasan->id_penugasan));

        $update = ProgressProyekVendor::where('id_penugasan', $penugasan->id_penugasan)
            ->where('status', 'submitted')
            ->firstOrFail();

        $this->assertCount(2, $update->foto_paths);
        Storage::disk('public')->assertExists($update->foto_paths[0]);
        Storage::disk('public')->assertExists($update->foto_paths[1]);
    }

    public function test_admin_project_detail_shows_submitted_progress_update(): void
    {
        [$vendorUser, $penugasan, $proyek, , $adminUser] = $this->createAssignedExecutionProject();

        $this->actingAs($vendorUser)
            ->post(route('vendor.proyek.progress.store', $penugasan->id_penugasan), [
                'persentase' => 65,
                'deskripsi' => 'Progress terlihat untuk admin.',
                'status_progress' => 'berjalan',
            ]);

        $response = $this->actingAs($adminUser)->get(route('proyek.admin.show', $proyek->id));

        $response->assertOk();
        $response->assertSee('Monitoring Progress Vendor');
        $response->assertSee('Progress terlihat untuk admin.');
        $response->assertSee('65%');
    }

    // TC-03: foto opsional
    public function test_submit_without_photo_succeeds_because_foto_is_optional(): void
    {
        [$vendorUser, $penugasan] = $this->createAssignedExecutionProject();

        $response = $this->actingAs($vendorUser)
            ->post(route('vendor.proyek.progress.store', $penugasan->id_penugasan), [
                'persentase' => 30,
                'deskripsi' => 'Update tanpa foto.',
                'status_progress' => 'berjalan',
            ]);

        $response->assertRedirect(route('vendor.proyek.progress.show', $penugasan->id_penugasan));
        $this->assertDatabaseHas('progress_proyek_vendor', [
            'id_penugasan' => $penugasan->id_penugasan,
            'persentase' => 30,
            'status' => 'submitted',
        ]);
    }

    // TC-05: persentase < 0
    public function test_submitted_progress_rejects_negative_percentage(): void
    {
        [$vendorUser, $penugasan] = $this->createAssignedExecutionProject();

        $response = $this->actingAs($vendorUser)
            ->from(route('vendor.proyek.progress.show', $penugasan->id_penugasan))
            ->post(route('vendor.proyek.progress.store', $penugasan->id_penugasan), [
                'persentase' => -5,
                'deskripsi' => 'Persentase negatif tidak valid.',
                'status_progress' => 'berjalan',
            ]);

        $response->assertRedirect(route('vendor.proyek.progress.show', $penugasan->id_penugasan));
        $response->assertSessionHasErrors('persentase');
    }

    // TC-08: donatur (non-vendor) akses form → 403
    public function test_donatur_gets_forbidden_accessing_progress_form(): void
    {
        [, $penugasan] = $this->createAssignedExecutionProject();

        $donaturUser = User::factory()->create(['role' => 'donatur']);

        $response = $this->actingAs($donaturUser)
            ->get(route('vendor.proyek.progress.show', $penugasan->id_penugasan));

        $response->assertForbidden();
    }

    // TC-10: riwayat tampil kronologis (terbaru di atas)
    public function test_progress_history_is_ordered_newest_first(): void
    {
        [$vendorUser, $penugasan] = $this->createAssignedExecutionProject();

        $this->actingAs($vendorUser)
            ->post(route('vendor.proyek.progress.store', $penugasan->id_penugasan), [
                'persentase' => 20,
                'deskripsi' => 'Update pertama.',
                'status_progress' => 'berjalan',
            ]);

        $this->travel(1)->minutes();

        $this->actingAs($vendorUser)
            ->post(route('vendor.proyek.progress.store', $penugasan->id_penugasan), [
                'persentase' => 50,
                'deskripsi' => 'Update kedua.',
                'status_progress' => 'berjalan',
            ]);

        $this->travel(1)->minutes();

        $this->actingAs($vendorUser)
            ->post(route('vendor.proyek.progress.store', $penugasan->id_penugasan), [
                'persentase' => 80,
                'deskripsi' => 'Update ketiga.',
                'status_progress' => 'berjalan',
            ]);

        $updates = $penugasan->fresh()->submittedProgressUpdates;

        $this->assertCount(3, $updates);
        $this->assertTrue($updates[0]->submitted_at >= $updates[1]->submitted_at);
        $this->assertTrue($updates[1]->submitted_at >= $updates[2]->submitted_at);
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

    private function createOtherVendor(): array
    {
        $penyedia = PenyediaEnergi::create([
            'nama' => 'Vendor Lain',
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
