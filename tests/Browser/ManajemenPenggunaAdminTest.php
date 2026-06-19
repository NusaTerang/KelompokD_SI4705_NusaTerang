<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ManajemenPenggunaAdminTest extends DuskTestCase
{
    public function test_tc_mp_01_melihat_daftar_pengguna(): void
    {
        $admin = User::where('email', 'admin@nusaterang.id')->first();

        $this->assertNotNull(
            $admin,
            'Admin tidak ditemukan'
        );

        $this->browse(function (Browser $browser) use ($admin) {

            $browser->loginAs($admin)

                    ->visit('/admin/dashboard')
                    ->pause(3000)

                    ->clickLink('Manajemen Pengguna')
                    ->pause(3000)

                    ->assertPathIs('/admin/users')

                    ->assertSee('Manajemen Pengguna')
                    ->assertSee('Nama Pengguna')
                    ->assertSee('Email')
                    ->assertSee('Role')
                    ->assertSee('Status')
                    ->assertSee('Tanggal Registrasi')

                    ->pause(5000);
        });
    }

    public function test_tc_mp_02_melihat_detail_pengguna(): void
    {
        $admin = User::where('email', 'admin@nusaterang.id')->first();

        $this->assertNotNull($admin);

        $this->browse(function (Browser $browser) use ($admin) {

            $browser->loginAs($admin)

                    ->visit('/admin/users')
                    ->pause(3000)

                    ->clickLink('Lihat Detail')
                    ->pause(3000)

                    ->assertSee('Detail Pengguna')
                    ->assertSee('Informasi Akun')
                    ->assertSee('Informasi Role')
                    ->assertSee('Status Akun')

                    ->pause(5000);
        });
    }

    public function test_tc_mp_03_mengubah_role_pengguna(): void
    {
        $admin = User::where('email', 'admin@nusaterang.id')->first();

        $this->assertNotNull($admin);

        $this->browse(function (Browser $browser) use ($admin) {

            $browser->loginAs($admin)

                    ->visit('/admin/users/8')
                    ->pause(3000)

                    ->press('Ubah Role')
                    ->pause(2000)

                    ->radio('role', 'penyedia')
                    ->pause(1000)

                    ->press('Simpan Perubahan')
                    ->pause(3000)

                    ->assertSee('berhasil')

                    ->pause(5000);
        });
    }

    public function test_tc_mp_04_menonaktifkan_akun_pengguna(): void
    {
        $admin = User::where('email', 'admin@nusaterang.id')->first();

        $this->assertNotNull($admin);

        $this->browse(function (Browser $browser) use ($admin) {

            $browser->loginAs($admin)

                    ->visit('/admin/users/8')
                    ->pause(3000)

                    ->press('Nonaktifkan Akun')
                    ->pause(2000)

                    ->press('Ya, Nonaktifkan')
                    ->pause(3000)

                    ->assertSee('berhasil')

                    ->pause(5000);
        });
    }
}