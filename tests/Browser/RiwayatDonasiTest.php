<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RiwayatDonasiTest extends DuskTestCase
{
    /**
     * TC-RD-001
     * Donatur dapat melihat halaman profil dan riwayat donasi.
     */
    public function test_tc_rd_001_halaman_profil_melalui_menu_profil(): void
    {
        $user = User::where('email', 'aditya.pratama@example.com')->first();

        $this->assertNotNull($user);

        $this->browse(function (Browser $browser) use ($user) {

            $browser->loginAs($user)
                    ->visit('/')
                    ->pause(3000)

                    // buka dropdown profil
                    ->click('button[aria-label="Menu Profil"]')
                    ->pause(2000)

                    // klik Profil Saya
                    ->clickLink('Profil Saya')
                    ->pause(3000)

                    ->assertPathIs('/profil')
                    ->assertSee('Riwayat Donasi')

                    ->pause(5000);
        });
    }

    /**
     * TC-RD-002
     * Donatur dapat melihat detail donasi.
     */
    public function test_tc_rd_002_melihat_detail_donasi(): void
    {
        $user = User::where('email', 'aditya.pratama@example.com')->first();

        $this->assertNotNull(
            $user,
            'User aditya.pratama@example.com tidak ditemukan'
        );

        $this->browse(function (Browser $browser) use ($user) {

            $browser->loginAs($user)
                    ->visit('/profil')
                    ->pause(2000)

                    ->clickLink('Lihat Detail')
                    ->pause(7000)

                    ->assertSee('Detail Transaksi Donasi')
                    ->assertSee('ID Donasi')
                    ->assertSee('Proyek')
                    ->assertSee('Nominal')
                    ->assertSee('Status')
                    ->assertSee('Tanggal Donasi')
                    ->assertSee('Status Refund')
                    ->pause(10000);
        });
    }

    /**
     * TC-RD-003
     * Donatur yang belum pernah donasi melihat empty state.
     */
    public function test_tc_rd_003_kembali_ke_halaman_profil(): void
    {
        $user = User::where('email', 'aditya.pratama@example.com')->first();

        $this->assertNotNull(
            $user,
            'User aditya.pratama@example.com tidak ditemukan'
        );

        $this->browse(function (Browser $browser) use ($user) {

            $browser->loginAs($user)
                    ->visit('/profil')
                    ->pause(5000)

                    ->clickLink('Lihat Detail')
                    ->pause(5000)

                    ->clickLink('Kembali')
                    ->pause(5000)

                    ->assertPathIs('/profil')
                    ->assertSee('Riwayat Donasi')
                    ->pause(10000);
        });
    }
}