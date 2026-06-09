<?php

namespace Tests\Feature;

use App\Models\Desa;
use App\Models\PenugasanProyek;
use App\Models\PenyediaEnergi;
use App\Models\Proyek;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ExpiredProjectExtensionTest extends TestCase
{
    use RefreshDatabase;

    private function makeDesa(): Desa
    {
        return Desa::create([
            'nama_desa' => 'Desa Expired',
            'provinsi' => 'Jawa Barat',
            'kabupaten' => 'Bandung',
            'kondisi_desa' => 'off-grid',
            'sumber' => 'solar_panel',
        ]);
    }

    private function makeVendorProject(string $status = 'menunggu_keputusan_vendor'): array
    {
        $penyedia = PenyediaEnergi::create([
            'nama' => 'Vendor Expired',
            'spesialisasi' => 'panel_surya',
            'provinsi_operasi' => 'Jawa Barat',
            'kisaran_harga_min' => 1000000,
            'kisaran_harga_max' => 10000000,
            'status' => 'aktif',
        ]);
        $user = User::factory()->create([
            'role' => 'penyedia',
            'penyedia_id' => $penyedia->id,
        ]);
        $desa = $this->makeDesa();
        $proyek = Proyek::create([
            'desa_id' => $desa->id_desa,
            'penyedia_id' => $penyedia->id,
            'judul' => 'Vendor Decision Project',
            'deskripsi' => 'Need decision.',
            'jenis_energi' => 'panel_surya',
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

        return [$user, $proyek, $penugasan];
    }

    /** TC-01 */
    public function test_cron_job_proyek_underfunded_batas_waktu_habis(): void
    {
        Notification::fake();

        $penyedia = PenyediaEnergi::create([
            'nama' => 'Vendor TC01',
            'spesialisasi' => 'panel_surya',
            'provinsi_operasi' => 'Jawa Barat',
            'kisaran_harga_min' => 1000000,
            'kisaran_harga_max' => 10000000,
            'status' => 'aktif',
        ]);
        $vendor = User::factory()->create([
            'role' => 'penyedia',
            'penyedia_id' => $penyedia->id,
        ]);
        $admin = User::factory()->create(['role' => 'admin']);
        $desa = $this->makeDesa();

        $proyek = Proyek::create([
            'desa_id' => $desa->id_desa,
            'penyedia_id' => $penyedia->id,
            'judul' => 'Proyek Expired TC01',
            'deskripsi' => 'Butuh keputusan vendor.',
            'jenis_energi' => 'panel_surya',
            'estimasi_mulai' => now()->subMonth()->toDateString(),
            'estimasi_selesai' => now()->subDay()->toDateString(),
            'status' => 'aktif_funding',
            'dana_terkumpul' => 5000000,
            'target_dana' => 100000000,
        ]);

        $this->artisan('proyek:extend-expired')->assertExitCode(0);

        $proyek->refresh();
        $this->assertSame('menunggu_keputusan_vendor', $proyek->status);
        $this->assertTrue($proyek->expired_extension_pending);

        Notification::assertSentTo($vendor, \App\Notifications\ProyekDitugaskan::class);
        Notification::assertSentTo($admin, \App\Notifications\ProyekDitugaskan::class);
    }

    /** TC-02 */
    public function test_cron_job_tidak_proses_proyek_funded(): void
    {
        $desa = $this->makeDesa();

        $proyek = Proyek::create([
            'desa_id' => $desa->id_desa,
            'judul' => 'Proyek Sudah Funded',
            'deskripsi' => 'Already funded and moved.',
            'jenis_energi' => 'panel_surya',
            'estimasi_mulai' => now()->subMonth()->toDateString(),
            'estimasi_selesai' => now()->subDay()->toDateString(),
            'status' => 'eksekusi',
            'dana_terkumpul' => 100000000,
            'target_dana' => 100000000,
        ]);

        $this->artisan('proyek:extend-expired')->assertExitCode(0);

        $this->assertNotSame('menunggu_keputusan_vendor', $proyek->fresh()->status);
        $this->assertFalse($proyek->fresh()->expired_extension_pending);
    }

    /** TC-03 */
    public function test_vendor_memutuskan_lanjutkan(): void
    {
        Notification::fake();

        [$user, $proyek, $penugasan] = $this->makeVendorProject();
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)
            ->post(route('vendor.proyek.expiry-decision', $penugasan->id_penugasan), [
                'decision' => 'continue',
            ]);

        $response->assertRedirect(route('vendor.proyek.index'));

        $proyek->refresh();
        $this->assertSame('selesai', $proyek->status);
        $this->assertSame('continue', $proyek->expired_vendor_decision);

        Notification::assertSentTo($admin, \App\Notifications\ProyekSelesai::class);
    }

    /** TC-04 */
    public function test_vendor_memutuskan_batalkan(): void
    {
        Notification::fake();

        [$user, $proyek, $penugasan] = $this->makeVendorProject();
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)
            ->post(route('vendor.proyek.expiry-decision', $penugasan->id_penugasan), [
                'decision' => 'refund',
            ]);

        $response->assertRedirect(route('vendor.proyek.index'));

        $proyek->refresh();
        $this->assertSame('refund', $proyek->status);
        $this->assertSame('refund', $proyek->expired_vendor_decision);

        Notification::assertSentTo($admin, \App\Notifications\LaporanRefundAdmin::class);
    }

    /** TC-05 */
    public function test_validasi_hak_akses_keputusan(): void
    {
        [$vendorUser, $proyek, $penugasan] = $this->makeVendorProject();

        // Donatur cannot access expiry decision
        $donatur = User::factory()->create(['role' => 'donatur']);
        $this->actingAs($donatur)
            ->post(route('vendor.proyek.expiry-decision', $penugasan->id_penugasan), [
                'decision' => 'continue',
            ])
            ->assertForbidden();

        // Admin cannot access expiry decision (route protected by penyedia middleware)
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)
            ->post(route('vendor.proyek.expiry-decision', $penugasan->id_penugasan), [
                'decision' => 'continue',
            ])
            ->assertForbidden();

        // A different penyedia vendor cannot make decision for another vendor's project
        $otherPenyedia = PenyediaEnergi::create([
            'nama' => 'Other Vendor',
            'spesialisasi' => 'panel_surya',
            'provinsi_operasi' => 'Jawa Tengah',
            'kisaran_harga_min' => 1000000,
            'kisaran_harga_max' => 10000000,
            'status' => 'aktif',
        ]);
        $otherVendor = User::factory()->create([
            'role' => 'penyedia',
            'penyedia_id' => $otherPenyedia->id,
        ]);
        $this->actingAs($otherVendor)
            ->post(route('vendor.proyek.expiry-decision', $penugasan->id_penugasan), [
                'decision' => 'continue',
            ])
            ->assertNotFound();
    }

    /** TC-06 */
    public function test_cron_reminder_3_hari_vendor(): void
    {
        Notification::fake();

        $penyedia = PenyediaEnergi::create([
            'nama' => 'Vendor Reminder',
            'spesialisasi' => 'panel_surya',
            'provinsi_operasi' => 'Jawa Barat',
            'kisaran_harga_min' => 1000000,
            'kisaran_harga_max' => 10000000,
            'status' => 'aktif',
        ]);
        $vendor = User::factory()->create([
            'role' => 'penyedia',
            'penyedia_id' => $penyedia->id,
        ]);
        $desa = $this->makeDesa();

        Proyek::create([
            'desa_id' => $desa->id_desa,
            'penyedia_id' => $penyedia->id,
            'judul' => 'Proyek Reminder TC06',
            'deskripsi' => 'Pending decision for 3 days.',
            'jenis_energi' => 'panel_surya',
            'estimasi_mulai' => now()->subMonth()->toDateString(),
            'estimasi_selesai' => now()->addDays(27)->toDateString(),
            'status' => 'menunggu_keputusan_vendor',
            'expired_extension_pending' => true,
            'expired_extended_at' => now()->subDays(3),
        ]);

        $this->artisan('proyek:remind-pending-decision')->assertExitCode(0);

        Notification::assertSentTo($vendor, \App\Notifications\ProyekDitugaskan::class);
    }
}
