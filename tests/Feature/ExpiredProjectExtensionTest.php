<?php

namespace Tests\Feature;

use App\Models\Desa;
use App\Models\PenugasanProyek;
use App\Models\PenyediaEnergi;
use App\Models\Proyek;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_command_extends_expired_active_funding_project_for_30_days(): void
    {
        $desa = $this->makeDesa();
        $oldEndDate = now()->subDay()->toDateString();

        $proyek = Proyek::create([
            'desa_id' => $desa->id_desa,
            'judul' => 'Proyek Expired',
            'deskripsi' => 'Butuh keputusan vendor.',
            'jenis_energi' => 'panel_surya',
            'estimasi_mulai' => now()->subMonth()->toDateString(),
            'estimasi_selesai' => $oldEndDate,
            'status' => 'aktif_funding',
            'dana_terkumpul' => 5000000,
        ]);

        $this->artisan('proyek:extend-expired')->assertExitCode(0);

        $proyek->refresh();
        $this->assertSame('menunggu_keputusan_vendor', $proyek->status);
        $this->assertTrue($proyek->expired_extension_pending);
        $this->assertSame($oldEndDate, $proyek->expired_original_end_date->toDateString());
        $this->assertSame(now()->addDays(30)->toDateString(), $proyek->estimasi_selesai->toDateString());
        $this->assertNotNull($proyek->expired_extended_at);
    }

    public function test_command_creates_vendor_assignment_for_expired_project_with_penyedia(): void
    {
        $desa = $this->makeDesa();
        $penyedia = PenyediaEnergi::create([
            'nama' => 'Vendor Assigned',
            'spesialisasi' => 'panel_surya',
            'provinsi_operasi' => 'Jawa Barat',
            'kisaran_harga_min' => 1000000,
            'kisaran_harga_max' => 10000000,
            'status' => 'aktif',
        ]);
        $proyek = Proyek::create([
            'desa_id' => $desa->id_desa,
            'penyedia_id' => $penyedia->id,
            'judul' => 'Expired Assigned Project',
            'deskripsi' => 'Need assignment.',
            'jenis_energi' => 'panel_surya',
            'estimasi_mulai' => now()->subMonth()->toDateString(),
            'estimasi_selesai' => now()->subDay()->toDateString(),
            'status' => 'aktif_funding',
        ]);

        $this->artisan('proyek:extend-expired')->assertExitCode(0);

        $this->assertDatabaseHas('penugasan_proyek', [
            'id_proyek' => $proyek->id,
            'id_penyedia' => $penyedia->id,
            'status_penugasan' => 'diterima',
        ]);
    }

    public function test_command_does_not_extend_non_expired_or_non_active_projects(): void
    {
        $desa = $this->makeDesa();

        $future = Proyek::create([
            'desa_id' => $desa->id_desa,
            'judul' => 'Future Project',
            'deskripsi' => 'Not expired.',
            'jenis_energi' => 'panel_surya',
            'estimasi_mulai' => now()->toDateString(),
            'estimasi_selesai' => now()->addDays(5)->toDateString(),
            'status' => 'aktif_funding',
        ]);

        $draft = Proyek::create([
            'desa_id' => $desa->id_desa,
            'judul' => 'Draft Project',
            'deskripsi' => 'Expired but draft.',
            'jenis_energi' => 'panel_surya',
            'estimasi_mulai' => now()->subMonth()->toDateString(),
            'estimasi_selesai' => now()->subDay()->toDateString(),
            'status' => 'draft',
        ]);

        $this->artisan('proyek:extend-expired')->assertExitCode(0);

        $this->assertSame('aktif_funding', $future->fresh()->status);
        $this->assertFalse($future->fresh()->expired_extension_pending);
        $this->assertSame('draft', $draft->fresh()->status);
        $this->assertFalse($draft->fresh()->expired_extension_pending);
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

    public function test_vendor_can_choose_refund_for_extended_project(): void
    {
        [$user, $proyek, $penugasan] = $this->makeVendorProject();

        $response = $this->actingAs($user)
            ->post(route('vendor.proyek.expiry-decision', $penugasan->id_penugasan), [
                'decision' => 'refund',
            ]);

        $response->assertRedirect(route('vendor.proyek.index'));
        $proyek->refresh();
        $this->assertSame('refund', $proyek->status);
        $this->assertSame('refund', $proyek->expired_vendor_decision);
        $this->assertFalse($proyek->expired_extension_pending);
    }

    public function test_vendor_can_continue_extended_project_with_existing_donation_amount(): void
    {
        [$user, $proyek, $penugasan] = $this->makeVendorProject();

        $response = $this->actingAs($user)
            ->post(route('vendor.proyek.expiry-decision', $penugasan->id_penugasan), [
                'decision' => 'continue',
            ]);

        $response->assertRedirect(route('vendor.proyek.index'));
        $proyek->refresh();
        $this->assertSame('selesai', $proyek->status);
        $this->assertSame('continue', $proyek->expired_vendor_decision);
        $this->assertFalse($proyek->expired_extension_pending);
        $this->assertEquals(7500000, $proyek->dana_terkumpul);
    }

    public function test_vendor_sees_dedicated_expiry_decision_page(): void
    {
        [$user, $proyek, $penugasan] = $this->makeVendorProject();

        $response = $this->actingAs($user)
            ->get(route('vendor.proyek.expiry-decision.show', $penugasan->id_penugasan));

        $response->assertOk();
        $response->assertSee('Keputusan Proyek Berakhir');
        $response->assertSee('Vendor Decision Project');
        $response->assertSee('Rp 7.500.000');
        $response->assertSee('Ajukan Refund');
        $response->assertSee('Lanjutkan Proyek');
        $response->assertDontSee('Rincian Teknis Proyek');
    }

    public function test_vendor_project_detail_no_longer_shows_expiry_decision_actions(): void
    {
        [$user, $proyek, $penugasan] = $this->makeVendorProject();

        $response = $this->actingAs($user)
            ->get(route('vendor.proyek.show', $penugasan->id_penugasan));

        $response->assertOk();
        $response->assertDontSee('Ajukan Refund');
        $response->assertDontSee('Lanjutkan Proyek');
    }

    public function test_vendor_project_index_links_pending_decision_to_dedicated_page(): void
    {
        [$user, $proyek, $penugasan] = $this->makeVendorProject();

        $response = $this->actingAs($user)
            ->get(route('vendor.proyek.index'));

        $response->assertOk();
        $response->assertSee('Tinjau Keputusan');
        $response->assertSee(route('vendor.proyek.expiry-decision.show', $penugasan->id_penugasan));
    }

    public function test_vendor_project_index_shows_refund_project_as_terminal_without_detail_action(): void
    {
        [$user, $proyek, $penugasan] = $this->makeVendorProject('refund');
        $proyek->update([
            'expired_extension_pending' => false,
            'expired_vendor_decision' => 'refund',
        ]);

        $response = $this->actingAs($user)
            ->get(route('vendor.proyek.index'));

        $response->assertOk();
        $response->assertSee('Refund');
        $response->assertDontSee('Belum Diisi');
        $response->assertDontSee('Diterima');
        $response->assertDontSee('Lihat & Isi Rincian');
        $response->assertDontSee(route('vendor.proyek.show', $penugasan->id_penugasan));
    }

    public function test_vendor_project_index_shows_completed_project_as_terminal_without_detail_action(): void
    {
        [$user, $proyek, $penugasan] = $this->makeVendorProject('selesai');
        $proyek->update([
            'expired_extension_pending' => false,
            'expired_vendor_decision' => 'continue',
        ]);

        $response = $this->actingAs($user)
            ->get(route('vendor.proyek.index'));

        $response->assertOk();
        $response->assertSee('Selesai');
        $response->assertDontSee('Belum Diisi');
        $response->assertDontSee('Diterima');
        $response->assertDontSee('Lihat & Isi Rincian');
        $response->assertDontSee(route('vendor.proyek.show', $penugasan->id_penugasan));
    }

    public function test_vendor_dashboard_shows_expiry_decision_notification(): void
    {
        [$user] = $this->makeVendorProject();

        $response = $this->actingAs($user)->get(route('vendor.dashboard'));

        $response->assertOk();
        $response->assertSee('1 proyek menunggu keputusan refund atau lanjut');
        $response->assertSee('Tinjau Sekarang');
    }
}
