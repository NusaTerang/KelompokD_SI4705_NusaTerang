<?php

namespace Tests\Browser;

use App\Models\User;
// use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Browser\ProyekTest\Concerns\CreatesProyekFixtures;

class AktivasiOtomatisTest extends DuskTestCase
{
    use CreatesProyekFixtures;

    protected function setUp(): void
    {
        parent::setUp();
        // \App\Models\User::query()->delete();
        // \App\Models\Proyek::query()->delete();
        // \App\Models\Desa::query()->delete();
        // \App\Models\PenyediaEnergi::query()->delete();
        // \App\Models\PenugasanProyek::query()->delete();
    }

    /**
     * @group AktivasiOtomatis
     */
    public function test_akses_donasi_tanpa_login_redirect(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->logout()
                    ->visit('/donasi')
                    ->assertPathIs('/login');
        });
    }

    /**
     * @group AktivasiOtomatis
     */
    public function test_tampilan_proyek_aktif_funding(): void
    {
        $proyekFunding = $this->createProject([
            'status' => 'aktif_funding',
            'target_dana' => 10000000,
            'deskripsi' => 'Deskripsi proyek ini sangat panjang dan detail.'
        ]);

        $this->browse(function (Browser $browser) use ($proyekFunding) {
            $browser->visit('/proyek/' . $proyekFunding->id)
                    ->assertSee($proyekFunding->judul)
                    ->assertSee($proyekFunding->desa->nama_desa)
                    ->assertSee('Deskripsi proyek ini sangat panjang dan detail.')
                    ->assertSee('TERKUMPUL')
                    ->assertSee('10.000.000')
                    ->assertSee('Login untuk Donasi');
        });
    }

    /**
     * @group AktivasiOtomatis
     */
    public function test_tampilan_proyek_sedang_eksekusi(): void
    {
        $proyekEksekusi = $this->createProject(['status' => 'eksekusi']);
        $vendorUser = $this->createVendorUser();
        $penugasanEks = \App\Models\PenugasanProyek::create([
            'id_proyek' => $proyekEksekusi->id,
            'id_penyedia' => $vendorUser->penyedia_id,
            'status_penugasan' => 'diterima',
        ]);
        \App\Models\ProgressProyekVendor::create([
            'id_penugasan' => $penugasanEks->id_penugasan,
            'persentase' => 45,
            'deskripsi' => 'Pemasangan rangka dasar panel surya dan persiapan kabel.',
            'status_progress' => 'berjalan',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $this->browse(function (Browser $browser) use ($proyekEksekusi) {
            $browser->visit('/proyek/' . $proyekEksekusi->id)
                    ->assertSee('Sedang Berjalan')
                    ->assertSee('45%')
                    ->assertSee('Pemasangan rangka dasar');
        });
    }

    /**
     * @group AktivasiOtomatis
     */
    public function test_tampilan_proyek_selesai(): void
    {
        $proyekSelesai = $this->createProject(['status' => 'selesai']);
        $vendorUser = $this->createVendorUser();
        $penugasanSel = \App\Models\PenugasanProyek::create([
            'id_proyek' => $proyekSelesai->id,
            'id_penyedia' => $vendorUser->penyedia_id,
            'status_penugasan' => 'diterima',
        ]);
        \App\Models\ProgressProyekVendor::create([
            'id_penugasan' => $penugasanSel->id_penugasan,
            'persentase' => 100,
            'deskripsi' => 'Semua panel surya dan baterai telah terpasang dan berfungsi 100%.',
            'status_progress' => 'selesai',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $this->browse(function (Browser $browser) use ($proyekSelesai) {
            $browser->visit('/proyek/' . $proyekSelesai->id)
                    ->assertSee('Selesai')
                    ->assertSee('100%');
        });
    }

    /**
     * @group AktivasiOtomatis
     */
    public function test_alur_donasi_via_halaman_proyek(): void
    {
        $proyekFunding = $this->createProject(['status' => 'aktif_funding']);
        $donatur = $this->createDonatur();
        $donatur->tambahSaldo(100000);

        $this->browse(function (Browser $browser) use ($proyekFunding, $donatur) {
            $browser->loginAs($donatur)
                    ->visit('/proyek/' . $proyekFunding->id)
                    ->type('total_price', '50000')
                    ->radio('temp_payment_method', 'saldo')
                    ->script("document.getElementById('hidden-payment-method').value='saldo';");
            $browser->click('#donasi-button');

            $browser->waitForLocation('/donasi')
                    ->assertPathBeginsWith('/donasi');
        });
    }
}
