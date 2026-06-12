<?php

namespace Tests\Browser\ProyekTest;

use App\Models\ProgressProyekVendor;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\Browser\ProyekTest\Concerns\CreatesProyekFixtures;
use Tests\DuskTestCase;

class PBI21VendorProjectProgressDuskTest extends DuskTestCase
{
    use CreatesProyekFixtures;
    use DatabaseTruncation;

    public function test_vendor_berhasil_submit_update_progres(): void
    {
        [$vendorUser, $penugasan, $proyek] = $this->createAssignedExecutionProject();

        $this->browse(function (Browser $browser) use ($vendorUser, $penugasan, $proyek) {
            $browser->loginAs($vendorUser)
                ->visitRoute('vendor.proyek.progress.show', $penugasan->id_penugasan)
                ->assertSee('Update Progress Proyek');

            $this->chooseRadio($browser, 'persentase', '50');
            $this->chooseRadio($browser, 'status_progress', 'berjalan');

            $browser
                ->type('deskripsi', 'Instalasi separuh selesai.')
                ->attach('fotos[]', $this->imageFixturePath())
                ->script("document.querySelector('form[action$=\"/progress\"]').submit()");

            $browser->waitForText('Update progress berhasil dikirim')
                ->visitRoute('proyek.show', $proyek->id)
                ->assertSee('50%')
                ->assertSee('Instalasi separuh selesai.');
        });

        $this->assertDatabaseHas('progress_proyek_vendor', [
            'id_penugasan' => $penugasan->id_penugasan,
            'persentase' => 50,
            'status_progress' => 'berjalan',
            'status' => 'submitted',
        ]);
    }

    public function test_validasi_persentase_di_luar_range(): void
    {
        [$vendorUser, $penugasan] = $this->createAssignedExecutionProject();

        $this->browse(function (Browser $browser) use ($vendorUser, $penugasan) {
            $browser->loginAs($vendorUser)
                ->visitRoute('vendor.proyek.progress.show', $penugasan->id_penugasan)
                ->type('deskripsi', 'Invalid percent.');

            $browser->script(
                "const input = document.querySelector('input[name=\"persentase\"]');"
                . "input.value = '110';"
                . "input.checked = true;"
                . "input.dispatchEvent(new Event('change', { bubbles: true }));"
            );
            $this->chooseRadio($browser, 'status_progress', 'berjalan');

            $browser
                ->attach('fotos[]', $this->imageFixturePath())
                ->script("document.querySelector('form[action$=\"/progress\"]').submit()");

            $browser->waitForText('Pilih tahapan progress yang tersedia.');
        });

        $this->assertDatabaseMissing('progress_proyek_vendor', [
            'id_penugasan' => $penugasan->id_penugasan,
            'persentase' => 110,
        ]);
    }

    public function test_pilih_status_selesai_munculkan_form_laporan_akhir(): void
    {
        [$vendorUser, $penugasan] = $this->createAssignedExecutionProject();

        $this->browse(function (Browser $browser) use ($vendorUser, $penugasan) {
            $browser->loginAs($vendorUser)
                ->visitRoute('vendor.proyek.progress.show', $penugasan->id_penugasan)
                ->assertSee('Update Progress Proyek');

            $this->chooseRadio($browser, 'persentase', '100');
            $this->chooseRadio($browser, 'status_progress', 'selesai');

            $browser
                ->type('deskripsi', 'Semua komponen sudah terpasang dan diuji.')
                ->attach('fotos[]', $this->imageFixturePath())
                ->script("document.querySelector('form[action$=\"/progress\"]').submit()");

            $browser->waitForText('Laporan Akhir')
                ->assertSee('Isi Laporan Akhir');
        });
    }

    public function test_vendor_simpan_draft(): void
    {
        [$vendorUser, $penugasan, $proyek] = $this->createAssignedExecutionProject();

        $this->browse(function (Browser $browser) use ($vendorUser, $penugasan, $proyek) {
            $browser->loginAs($vendorUser)
                ->visitRoute('vendor.proyek.progress.show', $penugasan->id_penugasan)
                ->assertSee('Update Progress Proyek');

            $this->chooseRadio($browser, 'persentase', '25');
            $this->chooseRadio($browser, 'status_progress', 'berjalan');

            $browser
                ->type('deskripsi', 'Panel mulai dipasang.')
                ->press('Simpan Draft')
                ->waitForText('Draft progress tersimpan')
                ->visitRoute('proyek.show', $proyek->id)
                ->assertDontSee('Panel mulai dipasang.');
        });

        $this->assertDatabaseHas('progress_proyek_vendor', [
            'id_penugasan' => $penugasan->id_penugasan,
            'persentase' => 25,
            'status' => 'draft',
        ]);
        $this->assertSame(0, ProgressProyekVendor::where('status', 'submitted')->count());
    }
}
