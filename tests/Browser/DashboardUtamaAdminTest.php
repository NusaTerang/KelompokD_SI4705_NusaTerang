<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
// use Illuminate\Foundation\Testing\DatabaseTruncation;
use Tests\Browser\ProyekTest\Concerns\CreatesProyekFixtures;
use App\Models\User;
use App\Models\Order;

class DashboardUtamaAdminTest extends DuskTestCase
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

    // Test kecil-kecil telah digabung ke test E2E di bawah

    /**
     * @group DashboardAdmin
     */
    public function test_akses_dashboard_tanpa_login_redirect(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->logout()
                    ->visit('/admin/dashboard')
                    ->assertPathIs('/login');
        });
    }

    /**
     * @group DashboardAdmin
     */
    public function test_proteksi_akses_non_admin(): void
    {
        $donatur = $this->createDonatur();

        $this->browse(function (Browser $browser) use ($donatur) {
            $browser->loginAs($donatur)
                    ->visit('/admin/dashboard')
                    ->assertSee('403');
        });
    }


    /**
     * @group DashboardAdmin
     */
    public function test_admin_dashboard_validasi_total_proyek(): void
    {
        $admin = $this->createAdmin();
        $desa = $this->createDesa($admin);
        
        // Buat 1 proyek awal
        $this->createProject(['admin' => $admin, 'desa_id' => $desa->id_desa]);

        $this->browse(function (Browser $browser) use ($admin, $desa) {
            $browser->loginAs($admin)
                    ->visitRoute('admin.dashboard')
                    ->assertSee('Total Proyek')
                    ->pause(1000);
            
            // Tambahkan +1 proyek di backend
            $this->createProject(['admin' => $admin, 'desa_id' => $desa->id_desa, 'judul' => 'Proyek Dusk ' . $desa->nama_desa]);

            // Refresh halaman dan cek apakah angka nambah jadi 2
            $browser->refresh()
                    ->pause(1000)
                    ->assertSee('2');
        });

        $this->assertDatabaseHas('proyeks', ['judul' => 'Proyek Dusk ' . $desa->nama_desa]);
    }

    /**
     * @group DashboardAdmin
     */
    public function test_admin_dashboard_validasi_desa_terverifikasi(): void
    {
        $admin = $this->createAdmin();
        // Buat 1 desa awal
        $this->createDesa($admin, ['nama_desa' => 'Desa Alpha']);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visitRoute('admin.dashboard')
                    ->assertSee('Desa Terverifikasi')
                    ->pause(1000);

            // Tambahkan +1 desa di backend
            $this->createDesa($admin, ['nama_desa' => 'Desa Beta']);

            // Refresh halaman dan cek nambah jadi 2
            $browser->refresh()
                    ->pause(1000)
                    ->assertSee('2');
        });

        $this->assertDatabaseHas('desa', ['nama_desa' => 'Desa Beta']);
    }

    /**
     * @group DashboardAdmin
     */
    public function test_admin_dashboard_validasi_donatur_aktif(): void
    {
        $admin = $this->createAdmin();
        $desa = $this->createDesa($admin);
        $donatur1 = $this->createDonatur();
        $donatur2 = $this->createDonatur();
        $proyek = $this->createProject(['admin' => $admin, 'desa_id' => $desa->id_desa]);

        // Donatur 1 melakukan donasi (Awalnya 1 donatur aktif)
        Order::create([
            'number' => 'ORD-' . uniqid(),
            'user_id' => $donatur1->id_donatur,
            'proyek_id' => $proyek->id,
            'total_price' => 150000,
            'payment_status' => Order::STATUS_SUCCESS,
        ]);

        $this->browse(function (Browser $browser) use ($admin, $donatur2, $proyek) {
            $browser->loginAs($admin)
                    ->visitRoute('admin.dashboard')
                    ->assertSee('Donatur Aktif')
                    ->pause(1000);
            
            // Tambahkan +1 donasi sukses dari donatur 2
            Order::create([
                'number' => 'ORD-' . uniqid(),
                'user_id' => $donatur2->id_donatur,
                'proyek_id' => $proyek->id,
                'total_price' => 200000,
                'payment_status' => Order::STATUS_SUCCESS,
            ]);

            // Refresh halaman dan cek donatur nambah jadi 2
            $browser->refresh()
                    ->pause(1000)
                    ->assertSee('2');
        });
    }

    /**
     * @group DashboardAdmin
     */
    public function test_admin_dashboard_validasi_proyek_terbaru(): void
    {
        $admin = $this->createAdmin();
        $desa = $this->createDesa($admin);
        $proyek = $this->createProject([
            'admin' => $admin,
            'desa_id' => $desa->id_desa,
            'judul' => 'PLTS Desa Makmur'
        ]);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visitRoute('admin.dashboard')
                    ->assertSee('Proyek Terbaru')
                    ->assertSee('PLTS Desa Makmur');
        });
    }

    /**
     * @group DashboardAdmin
     */
    public function test_admin_dashboard_validasi_donatur_terbaru(): void
    {
        $admin = $this->createAdmin();
        $desa = $this->createDesa($admin);
        $donatur = $this->createDonatur(['nama' => 'Bapak Budi Hartono']);
        $proyek = $this->createProject(['admin' => $admin, 'desa_id' => $desa->id_desa]);
        
        Order::create([
            'number' => 'ORD-' . uniqid(),
            'user_id' => $donatur->id_donatur,
            'proyek_id' => $proyek->id,
            'total_price' => 750000,
            'payment_status' => Order::STATUS_SUCCESS,
        ]);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visitRoute('admin.dashboard')
                    // Validasi Donatur Terbaru / Aktivitas Donasi
                    ->assertSee('Aktivitas Donasi')
                    ->assertSee('Bapak Budi Hartono')
                    ->assertSee('750.000'); // Validasi format uang
        });
    }

    /**
     * @group DashboardAdmin
     */
    public function test_admin_dashboard_tampilan_dan_filter(): void
    {
        $admin = $this->createAdmin(['nama' => 'Admin Super']);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visitRoute('admin.dashboard')
                    ->assertSee('Selamat datang kembali, Admin Super.')
                    ->assertPresent('select[name="period"]')
                    ->assertSee('Hari Ini')
                    ->assertSee('Semua Waktu')
                    ->select('select[name="period"]', '7days')
                    ->pause(1000)
                    ->assertQueryStringHas('period', '7days')
                    // Navigasi ke Kelola Proyek
                    ->visit('/admin/proyek/kelola')
                    ->assertSee('Kelola seluruh proyek energi');
        });
    }
}
