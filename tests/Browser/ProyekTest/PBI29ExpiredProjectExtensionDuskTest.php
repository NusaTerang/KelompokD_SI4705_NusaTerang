<?php

namespace Tests\Browser\ProyekTest;

use App\Models\PenyediaEnergi;
use App\Models\Proyek;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\Browser\ProyekTest\Concerns\CreatesProyekFixtures;
use Tests\DuskTestCase;

class PBI29ExpiredProjectExtensionDuskTest extends DuskTestCase
{
    use CreatesProyekFixtures;
    use DatabaseTruncation;

    public function test_cron_job_proyek_underfunded_batas_waktu_habis(): void
    {
        $penyedia = $this->createPenyedia(['nama' => 'Vendor TC01 Dusk']);
        $this->createVendorUser($penyedia);
        $this->createAdmin();
        $proyek = $this->createProject([
            'penyedia_id' => $penyedia->id,
            'judul' => 'Proyek Expired TC01 Dusk',
            'estimasi_mulai' => now()->subMonth()->toDateString(),
            'estimasi_selesai' => now()->subDay()->toDateString(),
            'status' => 'aktif_funding',
            'dana_terkumpul' => 5000000,
            'target_dana' => 100000000,
        ]);

        $this->artisan('proyek:extend-expired')->assertExitCode(0);

        $this->assertSame('menunggu_keputusan_vendor', $proyek->fresh()->status);
        $this->assertTrue((bool) $proyek->fresh()->expired_extension_pending);
    }

    public function test_cron_job_tidak_proses_proyek_funded(): void
    {
        $proyek = $this->createProject([
            'judul' => 'Proyek Sudah Funded Dusk',
            'estimasi_mulai' => now()->subMonth()->toDateString(),
            'estimasi_selesai' => now()->subDay()->toDateString(),
            'status' => 'eksekusi',
            'dana_terkumpul' => 100000000,
            'target_dana' => 100000000,
        ]);

        $this->artisan('proyek:extend-expired')->assertExitCode(0);

        $this->assertNotSame('menunggu_keputusan_vendor', $proyek->fresh()->status);
        $this->assertFalse((bool) $proyek->fresh()->expired_extension_pending);
    }

    public function test_vendor_memutuskan_lanjutkan(): void
    {
        [$vendor, $proyek, $penugasan] = $this->createExpiredVendorProject();

        $this->browse(function (Browser $browser) use ($vendor, $penugasan) {
            $browser->loginAs($vendor)
                ->visitRoute('vendor.proyek.expiry-decision.show', $penugasan->id_penugasan)
                ->assertSee('Keputusan Proyek Berakhir')
                ->press('Lanjutkan Proyek')
                ->waitForRoute('vendor.proyek.index')
                ->assertSee('Proyek dilanjutkan');
        });

        $this->assertSame('selesai', $proyek->fresh()->status);
        $this->assertSame('continue', $proyek->fresh()->expired_vendor_decision);
    }

    public function test_vendor_memutuskan_batalkan(): void
    {
        [$vendor, $proyek, $penugasan] = $this->createExpiredVendorProject();

        $this->browse(function (Browser $browser) use ($vendor, $penugasan) {
            $browser->loginAs($vendor)
                ->visitRoute('vendor.proyek.expiry-decision.show', $penugasan->id_penugasan)
                ->press('Ajukan Refund')
                ->waitForRoute('vendor.proyek.index')
                ->assertSee('Status proyek diubah menjadi refund');
        });

        $this->assertSame('refund', $proyek->fresh()->status);
        $this->assertSame('refund', $proyek->fresh()->expired_vendor_decision);
    }

    public function test_validasi_hak_akses_keputusan(): void
    {
        [, , $penugasan] = $this->createExpiredVendorProject();
        $donatur = $this->createDonatur();
        $admin = $this->createAdmin();
        $otherVendor = $this->createVendorUser($this->createPenyedia(['nama' => 'Other Vendor Dusk']));

        $this->browse(function (Browser $browser) use ($donatur, $admin, $otherVendor, $penugasan) {
            $browser->loginAs($donatur)
                ->visitRoute('vendor.proyek.expiry-decision.show', $penugasan->id_penugasan)
                ->assertSee('403');

            $browser->loginAs($admin)
                ->visitRoute('vendor.proyek.expiry-decision.show', $penugasan->id_penugasan)
                ->assertSee('403');

            $browser->loginAs($otherVendor)
                ->visitRoute('vendor.proyek.expiry-decision.show', $penugasan->id_penugasan)
                ->assertSee('404');
        });
    }

    public function test_cron_reminder_3_hari_vendor(): void
    {
        $penyedia = $this->createPenyedia(['nama' => 'Vendor Reminder Dusk']);
        $vendor = $this->createVendorUser($penyedia);
        $this->createProject([
            'penyedia_id' => $penyedia->id,
            'judul' => 'Proyek Reminder TC06 Dusk',
            'estimasi_mulai' => now()->subMonth()->toDateString(),
            'estimasi_selesai' => now()->addDays(27)->toDateString(),
            'status' => 'menunggu_keputusan_vendor',
            'expired_extension_pending' => true,
            'expired_extended_at' => now()->subDays(3),
        ]);

        $this->artisan('proyek:remind-pending-decision')->assertExitCode(0);

        $this->assertSame(1, $vendor->fresh()->notifications()->count());
    }
}
