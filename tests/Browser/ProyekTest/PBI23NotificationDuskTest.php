<?php

namespace Tests\Browser\ProyekTest;

use App\Models\PenugasanProyek;
use App\Models\User;
use App\Notifications\ProgressProyekDikirim;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\Browser\ProyekTest\Concerns\CreatesProyekFixtures;
use Tests\DuskTestCase;

class PBI23NotificationDuskTest extends DuskTestCase
{
    use CreatesProyekFixtures;
    use DatabaseTruncation;

    public function test_validasi_trigger_event_notifikasi(): void
    {
        [$vendorUser, $penugasan, $proyek] = $this->createAssignedExecutionProject();
        $donor = $this->createDonatur();
        $nonDonor = $this->createDonatur();
        $this->addSuccessfulDonation($proyek, $donor, 100000);

        $this->browse(function (Browser $browser) use ($vendorUser, $penugasan) {
            $browser->loginAs($vendorUser)
                ->visitRoute('vendor.proyek.progress.show', $penugasan->id_penugasan)
                ->assertSee('Update Progress Proyek');

            $this->chooseRadio($browser, 'persentase', '50');
            $this->chooseRadio($browser, 'status_progress', 'berjalan');

            $browser
                ->type('deskripsi', 'Panel mulai terpasang.')
                ->attach('fotos[]', $this->imageFixturePath())
                ->script("document.querySelector('form[action$=\"/progress\"]').submit()");

            $browser->waitForText('Update progress berhasil dikirim');
        });

        $this->assertSame(1, $donor->fresh()->notifications()->count());
        $this->assertSame(0, $nonDonor->fresh()->notifications()->count());
    }

    public function test_validasi_alur_interaksi_ui_notifikasi(): void
    {
        $user = $this->createDonatur();
        $proyek = $this->createProject(['judul' => 'Proyek Notifikasi Dusk']);

        $user->notify(new ProgressProyekDikirim($proyek));
        $user->notify(new ProgressProyekDikirim($proyek));
        $user->notify(new ProgressProyekDikirim($proyek));

        $this->browse(function (Browser $browser) use ($user, $proyek) {
            $browser->loginAs($user)
                ->visitRoute('notifications.index')
                ->assertSee('Notifikasi')
                ->assertSee('Update Progress Proyek')
                ->assertSee('BELUM DIBACA')
                ->press('Tandai semua dibaca')
                ->waitForRoute('notifications.index')
                ->assertSee('DIBACA')
                ->visitRoute('proyek.show', $proyek->id)
                ->assertSee('Proyek Notifikasi Dusk');
        });

        $this->assertSame(0, $user->fresh()->unreadNotifications()->count());
    }

    public function test_validasi_isolasi_data_notifikasi(): void
    {
        $donaturA = $this->createDonatur();
        $donaturB = $this->createDonatur();
        $proyekB = $this->createProject(['judul' => 'Notifikasi Donatur B']);
        $donaturB->notify(new ProgressProyekDikirim($proyekB));

        $this->browse(function (Browser $browser) use ($donaturA) {
            $browser->loginAs($donaturA)
                ->visitRoute('notifications.index')
                ->assertDontSee('Notifikasi Donatur B');
        });
    }

    public function test_validasi_tampilan_kosong(): void
    {
        $user = $this->createDonatur();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visitRoute('notifications.index')
                ->assertSee('Tidak ada notifikasi');
        });
    }
}
