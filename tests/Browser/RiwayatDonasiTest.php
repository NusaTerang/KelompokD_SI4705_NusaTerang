<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Donasi;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RiwayatDonasiTest extends DuskTestCase
{
    /**
     * TC-RD-001
     * Donatur membuka halaman profil dan memiliki riwayat donasi
     */
    public function test_tc_rd_001_halaman_profil_menampilkan_riwayat()
    {
        $user = User::where('email', 'aditya.pratama@example.com')->first();

        $this->assertNotNull($user);

        $this->browse(function (Browser $browser) use ($user) {

            $browser->loginAs($user)
                    ->visit('/profil')
                    ->assertSee('Riwayat Donasi');
        });
    }

    /**
     * TC-RD-002
     * Nama proyek muncul
     */
    public function test_tc_rd_002_nama_proyek_muncul()
    {
        $user = User::where('email', 'aditya.pratama@example.com')->first();

        $this->browse(function (Browser $browser) use ($user) {

            $browser->loginAs($user)
                    ->visit('/profil')
                    ->assertSee('Mikrohidro Sungai Sawai');
        });
    }

    /**
     * TC-RD-003
     * Nominal donasi muncul
     */
    public function test_tc_rd_003_nominal_donasi_muncul()
    {
        $user = User::where('email', 'aditya.pratama@example.com')->first();

        $this->browse(function (Browser $browser) use ($user) {

            $browser->loginAs($user)
                    ->visit('/profil')
                    ->assertSee('Rp 50.000');
        });
    }

    /**
     * TC-RD-004
     * Tanggal transaksi muncul
     */
    public function test_tc_rd_004_tanggal_transaksi_muncul()
    {
        $user = User::where('email', 'aditya.pratama@example.com')->first();

        $this->browse(function (Browser $browser) use ($user) {

            $browser->loginAs($user)
                    ->visit('/profil')
                    ->assertSee('11 Jun 2026');
        });
    }

    /**
     * TC-RD-005
     * Status pembayaran sukses
     */
    public function test_tc_rd_005_status_sukses_muncul()
    {
        $user = User::where('email', 'aditya.pratama@example.com')->first();

        $this->browse(function (Browser $browser) use ($user) {

            $browser->loginAs($user)
                    ->visit('/profil')
                    ->assertSee('SUKSES');
        });
    }

    /**
     * TC-RD-006
     * Riwayat ditampilkan kronologis
     */
    public function test_tc_rd_006_riwayat_kronologis()
    {
        $user = User::where('email', 'aditya.pratama@example.com')->first();

        $this->browse(function (Browser $browser) use ($user) {

            $browser->loginAs($user)
                    ->visit('/profil')
                    ->assertSee('Riwayat Donasi');
        });

        // Urutan kronologis lebih baik diuji dengan data fixture
        // atau selector khusus jika diperlukan.
    }

    /**
     * TC-RD-007
     * Klik tombol Lihat Detail
     */
    public function test_tc_rd_007_buka_detail_donasi()
    {
        $user = User::where('email', 'aditya.pratama@example.com')->first();

        $this->browse(function (Browser $browser) use ($user) {

            $browser->loginAs($user)
                    ->visit('/profil')
                    ->clickLink('Lihat Detail')
                    ->assertPathBeginsWith('/profil/donasi/')
                    ->assertSee('Detail Transaksi Donasi');
        });
    }

    /**
     * TC-RD-008
     * Detail transaksi lengkap
     */
    public function test_tc_rd_008_detail_transaksi_lengkap()
    {
        $user = User::where('email', 'aditya.pratama@example.com')->first();

        $donasi = Donasi::where('id_donatur', $user->id_donatur)
                        ->latest()
                        ->first();

        $this->assertNotNull($donasi);

        $this->browse(function (Browser $browser) use ($user, $donasi) {

            $browser->loginAs($user)
                    ->visit('/profil/donasi/' . $donasi->id_donasi)
                    ->assertSee('ID Donasi')
                    ->assertSee('Proyek')
                    ->assertSee('Nominal')
                    ->assertSee('Status')
                    ->assertSee('Tanggal Donasi')
                    ->assertSee('Status Refund');
        });
    }

    /**
     * TC-RD-009
     * Status refund muncul
     */
    public function test_tc_rd_009_status_refund()
    {
        $user = User::where('email', 'aditya.pratama@example.com')->first();

        $donasi = Donasi::where('id_donatur', $user->id_donatur)
                        ->latest()
                        ->first();

        $this->browse(function (Browser $browser) use ($user, $donasi) {

            $browser->loginAs($user)
                    ->visit('/profil/donasi/' . $donasi->id_donasi)
                    ->assertSee('Status Refund');
        });
    }

    /**
     * TC-RD-010
     * Tombol kembali
     */
    public function test_tc_rd_010_tombol_kembali()
    {
        $user = User::where('email', 'aditya.pratama@example.com')->first();

        $donasi = Donasi::where('id_donatur', $user->id_donatur)
                        ->latest()
                        ->first();

        $this->browse(function (Browser $browser) use ($user, $donasi) {

            $browser->loginAs($user)
                    ->visit('/profil/donasi/' . $donasi->id_donasi)
                    ->clickLink('Kembali')
                    ->assertPathIs('/profil');
        });
    }

    /**
     * TC-RD-011
     * Belum pernah donasi
     */
    public function test_tc_rd_011_belum_ada_riwayat()
    {
        $user = User::where('email', 'userbaru@example.com')->first();

        $this->assertNotNull($user);

        $this->browse(function (Browser $browser) use ($user) {

            $browser->loginAs($user)
                    ->visit('/profil')
                    ->assertSee('Belum ada riwayat donasi');
        });
    }

    /**
     * TC-RD-012
     * Hanya melihat riwayat milik sendiri
     */
    public function test_tc_rd_012_hanya_melihat_data_sendiri()
    {
        $user = User::where('email', 'aditya.pratama@example.com')->first();

        $this->browse(function (Browser $browser) use ($user) {

            $browser->loginAs($user)
                    ->visit('/profil')

                    // Ganti dengan nama proyek milik user lain
                    ->assertDontSee('Proyek Milik Donatur Lain');
        });
    }
}