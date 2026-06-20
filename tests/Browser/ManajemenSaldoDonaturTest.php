<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Tests\Browser\ProyekTest\Concerns\CreatesProyekFixtures;
use App\Models\MutasiSaldo;
use App\Models\User;
// use Illuminate\Foundation\Testing\DatabaseTruncation;

class ManajemenSaldoDonaturTest extends DuskTestCase
{
    use CreatesProyekFixtures;
    // use DatabaseTruncation;

    protected function setUp(): void
    {
        parent::setUp();
        foreach (static::$browsers as $browser) {
            $browser->driver->manage()->deleteAllCookies();
        }
    }

    /**
     * @group ManajemenSaldo
     */
    public function test_akses_saldo_tanpa_login_redirect(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->logout()
                    ->visit('/donatur/saldo')
                    ->assertPathIs('/login');
        });
    }

    /**
     * @group ManajemenSaldo
     */
    public function test_e2e_alur_topup_dan_penggunaan_saldo(): void
    {
        $donatur = $this->createDonatur(['nama' => 'Donatur E2E Saldo']);
        $proyek = $this->createProject([
            'status' => 'aktif_funding',
            'judul' => 'Proyek Surya 100kW'
        ]);

        $this->browse(function (Browser $browser) use ($donatur, $proyek) {
            // 1. Cek State Awal Saldo Kosong
            $browser->loginAs($donatur)
                    ->visit('/donatur/saldo')
                    ->assertSee('Saldo Saya')
                    ->assertSee('Belum ada riwayat saldo')
                    ->assertSee('Tidak ada masa kadaluarsa')
                    ->assertSee('Top Up Saldo');

            // 2. Melakukan Top Up
            $browser->visitRoute('donatur.topup.create')
                    ->assertSee('Top Up Saldo')
                    ->type('amount', '150000')
                    ->press('Lanjut Bayar QRIS')
                    ->pause(1000)
                    ->assertSee('Pembayaran Top Up');
        });

        // Simulasikan Midtrans webhook sukses
        $topup = \App\Models\Topup::where('user_id', $donatur->id_donatur)->latest()->first();
        $this->assertNotNull($topup, 'Topup record should be created in DB');
        $topup->confirmPaid();

        $this->browse(function (Browser $browser) use ($proyek) {
            // 3. Verifikasi Saldo Bertambah
            $browser->visit('/donatur/saldo')
                    ->assertSee('150.000') // Saldo berubah format
                    ->assertSee('+Rp 150.000'); // Mutasi masuk

            // 4. Gunakan Saldo untuk Donasi Penuh
            $browser->visit('/proyek/' . $proyek->id)
                    ->assertSee('Proyek Surya 100kW')
                    ->type('total_price', '50000')
                    ->radio('temp_payment_method', 'saldo')
                    ->press('Donasi Sekarang')
                    ->pause(1000)
                    // Masuk ke halaman Isi Data (/donatur/donasi/create)
                    ->assertSee('Donasi untuk NusaTerang')
                    ->radio('payment_method', 'saldo')
                    ->press('Lanjutkan ke Pembayaran')
                    ->pause(1000)
                    // Masuk ke halaman Konfirmasi
                    ->assertSee('Konfirmasi Donasi')
                    ->press('Konfirmasi Donasi')
                    ->pause(1000);
        });

        // Verifikasi DB Saldo Berkurang
        $this->assertDatabaseHas('saldo_donatur', [
            'id_donatur' => $donatur->id_donatur,
            'saldo' => 100000 // 150000 - 50000
        ]);
        
        $this->assertDatabaseHas('mutasi_saldo', [
            'id_donatur' => $donatur->id_donatur,
            'tipe' => 'keluar',
            'nominal' => 50000 // Uang keluar untuk donasi
        ]);
    }
    /**
     * @group ManajemenSaldo
     */
    public function test_validasi_tabel_riwayat_mutasi_saldo_tampil_lengkap(): void
    {
        $donatur = $this->createDonatur(['nama' => 'Donatur Riwayat']);
        
        // 1. Injeksi Mutasi Saldo: Top Up
        MutasiSaldo::create([
            'id_donatur' => $donatur->id_donatur,
            'tipe' => 'topup',
            'nominal' => 200000,
            'saldo_sebelum' => 0,
            'saldo_sesudah' => 200000,
            'keterangan' => 'Top Up Saldo #ORD-999',
            'created_at' => now()->subDays(2)
        ]);

        // 2. Injeksi Mutasi Saldo: Refund
        MutasiSaldo::create([
            'id_donatur' => $donatur->id_donatur,
            'tipe' => 'refund',
            'nominal' => 50000,
            'saldo_sebelum' => 200000,
            'saldo_sesudah' => 250000,
            'keterangan' => 'Refund Proyek PLTS Gagal',
            'created_at' => now()->subDays(1)
        ]);

        $this->browse(function (Browser $browser) use ($donatur) {
            $browser->loginAs($donatur)
                    ->visit('/donatur/saldo')
                    // Validasi tampilan Riwayat Top Up
                    ->assertSee('Top Up Saldo #ORD-999')
                    ->assertSee('+Rp 200.000')
                    // Validasi tampilan Riwayat Refund
                    ->assertSee('Refund Proyek PLTS Gagal')
                    ->assertSee('+Rp 50.000');
        });
        
        // Validasi database
        $this->assertDatabaseHas('mutasi_saldo', [
            'id_donatur' => $donatur->id_donatur,
            'tipe' => 'topup',
            'nominal' => 200000,
        ]);
        $this->assertDatabaseHas('mutasi_saldo', [
            'id_donatur' => $donatur->id_donatur,
            'tipe' => 'refund',
            'nominal' => 50000,
        ]);
    }
}
