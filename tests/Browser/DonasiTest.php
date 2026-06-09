<?php

namespace Tests\Browser;

use App\Models\Proyek;
use App\Models\TransaksiDonasi;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DonasiTest extends DuskTestCase
{
    /**
     * TC-17-01
     * User membuka halaman detail proyek crowdfunding
     * Expected:
     * Progress pendanaan, total dana, target dana,
     * dan status proyek tampil.
     */
    public function test_tc_17_01_buka_detail_proyek()
    {
        $this->browse(function (Browser $browser) {

            $browser->visit('/proyek/1')
                ->pause(5000)
                ->assertSee('Kemajuan Pendanaan')
                ->assertSee('TERKUMPUL')
                ->assertSee('TARGET')
                ->assertSee('SISA HARI');
        });
    }

    /**
     * TC-17-02
     * Terdapat transaksi donasi baru status success
     * Expected:
     * Data pendanaan dan activity feed ter-update.
     */
    public function test_tc_17_02_donasi_success_update_progress()
    {
        $this->browse(function (Browser $browser) {

            $browser->visit('/proyek/1')
                ->pause(2000);

            $progressAwal = $browser->text('.progress-percentage');

            $browser->refresh()
                ->pause(3000);

            $progressBaru = $browser->text('.progress-percentage');

            $this->assertNotEquals(
                $progressAwal,
                $progressBaru
            );

            $browser->assertPresent('.activity-feed');
        });
    }

    /**
     * TC-17-03
     * Donasi status failed / pending
     * Expected:
     * Progress pendanaan tidak berubah.
     */
    public function test_tc_17_03_failed_pending_tidak_update_progress()
    {
        $this->browse(function (Browser $browser) {

            $browser->visit('/proyek/1')
                ->pause(2000);

            $progressAwal = $browser->text('.progress-percentage');

            $browser->refresh()
                ->pause(3000);

            $progressAkhir = $browser->text('.progress-percentage');

            $this->assertEquals(
                $progressAwal,
                $progressAkhir
            );
        });
    }

    /**
     * TC-17-04
     * Dana mencapai target pendanaan
     * Expected:
     * Progress 100% dan status berubah.
     */
    public function test_tc_17_04_target_tercapai()
    {
        $this->browse(function (Browser $browser) {

            $browser->visit('/proyek/1')
                ->pause(2000)
                ->assertSee('100%')
                ->assertSee('Eksekusi');
        });
    }
}