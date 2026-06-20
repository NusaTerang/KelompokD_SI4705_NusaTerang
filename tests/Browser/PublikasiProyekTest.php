<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Tests\Browser\ProyekTest\Concerns\CreatesProyekFixtures;
use App\Models\Proyek;
use App\Models\User;
// use Illuminate\Foundation\Testing\DatabaseTruncation;

class PublikasiProyekTest extends DuskTestCase
{
    use CreatesProyekFixtures;
    // use DatabaseTruncation;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @group PublikasiProyek
     */
    public function test_akses_kelola_proyek_tanpa_login_redirect(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->logout()
                    ->visit('/admin/proyek/kelola')
                    ->assertPathIs('/login');
        });
    }

    /**
     * @group PublikasiProyek
     */
    public function test_akses_kelola_proyek_berhasil(): void
    {
        $admin = $this->createAdmin();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/proyek/kelola')
                    ->assertSee('Kelola Proyek Energi')
                    ->assertSee('Buat Proyek Baru');
        });
    }

    /**
     * @group PublikasiProyek
     */
    public function test_e2e_pembuatan_hingga_publikasi_proyek_baru_tanpa_revisi(): void
    {
        $admin = $this->createAdmin();
        $desa = $this->createDesa($admin);
        $penyedia = $this->createPenyedia();
        $vendorUser = $this->createVendorUser($penyedia);

        $uniqueTitle = 'Proyek E2E Happy Path ' . uniqid();
        $this->browse(function (Browser $browser) use ($admin, $desa, $penyedia, $vendorUser, $uniqueTitle) {
            // 1. Admin Login & Test Filter/Pencarian Dasar di Kelola Proyek
            $browser->loginAs($admin)
                    ->visit('/admin/proyek/kelola')
                    ->select('jenis_energi', 'panel_surya')
                    ->press('Filter')
                    ->pause(1000)
                    ->assertQueryStringHas('jenis_energi', 'panel_surya')
                    ->select('status', 'draft')
                    ->press('Filter')
                    ->pause(1000)
                    ->assertQueryStringHas('status', 'draft');

            // 2. Akses Form Pembuatan Proyek
            $browser->visit('/admin/proyek/buat')
                    ->assertSee('Buat Proyek');

            // 3. Validasi Form Kosong (Negative Check)
            $browser->script("document.querySelectorAll('[required]').forEach(el => el.removeAttribute('required'));");
            $browser->press('Lanjut Pilih Penyedia')
                    ->pause(1000)
                    ->assertPathIs('/admin/proyek/buat');

            // 4. Validasi Tanggal Masa Lalu (Negative Check)
            $browser->script("document.querySelector('input[name=\"estimasi_mulai\"]').type = 'text'; document.querySelector('input[name=\"estimasi_selesai\"]').type = 'text';");
            $browser->type('estimasi_mulai', '2020-01-01')
                    ->type('estimasi_selesai', '2019-01-01')
                    ->press('Lanjut Pilih Penyedia')
                    ->pause(1000)
                    ->assertPathIs('/admin/proyek/buat');

            // 5. Mengisi Form Step 1 Secara Valid (Happy Path)
            $browser->type('judul', $uniqueTitle)
                    ->select('desa_id', $desa->id_desa)
                    ->select('jenis_energi', 'panel_surya')
                    ->type('deskripsi', 'Ini adalah deskripsi proyek happy path tanpa revisi.');
            $browser->script("document.querySelector('input[name=\"estimasi_mulai\"]').type = 'text'; document.querySelector('input[name=\"estimasi_selesai\"]').type = 'text';");
            $browser->type('estimasi_mulai', now()->addDays(5)->format('Y-m-d'))
                    ->type('estimasi_selesai', now()->addDays(30)->format('Y-m-d'));
            $browser->attach('fotos[]', $this->imageFixturePath())
                    ->press('Lanjut Pilih Penyedia')
                    ->pause(2000);
            
            // Debug if validation fails
            if (str_contains($browser->driver->getCurrentURL(), '/admin/proyek/buat')) {
                $html = $browser->driver->getPageSource();
                if (preg_match('/<div class="p-4 bg-red-100.*<ul>(.*?)<\/ul>/s', $html, $matches)) {
                    dump("VALIDATION ERRORS: " . strip_tags($matches[1]));
                }
            }
            
            $browser->assertPathBeginsWith('/admin/proyek/');

            // 6. Pilih Penyedia (Step 2)
            $browser->script("
                var radio = document.querySelector('input[name=\"penyedia_id_radio\"][value=\"".$penyedia->id."\"]');
                if(radio) { radio.checked = true; radio.dispatchEvent(new Event('change', {bubbles:true})); }
                var hidden = document.getElementById('penyedia_id_hidden');
                if(hidden) hidden.value = '".$penyedia->id."';
            ");
            $browser->press('Simpan & Review')
                    ->pause(3000)
                    ->assertPathBeginsWith('/admin/proyek/');

            // 7. Kirim ke Penyedia (Step 3)
            $browser->press('Kirim ke Penyedia')
                    ->pause(2000)
                    ->assertPathIs('/admin/proyek/kelola')
                    ->assertSee($uniqueTitle);
        });

        // Tunggu bentar buat dapetin objek proyek yang baru dibikin (via judul)
        $proyek = \App\Models\Proyek::where('judul', $uniqueTitle)->first();
        $this->assertNotNull($proyek, 'Proyek gagal terbuat di database!');
        $penugasan = \App\Models\PenugasanProyek::where('id_proyek', $proyek->id)->first();
        
        // 7.5 Vendor Menerima dan Mengisi Rincian Teknis (UI)
        $this->browse(function (Browser $browser) use ($vendorUser, $penugasan) {
            $browser->logout()
                    ->loginAs($vendorUser)
                    ->visitRoute('vendor.proyek.show', $penugasan->id_penugasan)
                    ->pause(1500)
                    ->type('kapasitas_daya', '10')
                    ->select('satuan_daya', 'kW')
                    ->type('target_dana', '50000000')
                    ->type('cost_breakdown[0][nama]', 'Baterai dan Panel')
                    ->type('cost_breakdown[0][nominal]', '50000000')
                    ->type('durasi_minggu', '4')
                    ->type('catatan_teknis', 'Siap dilaksanakan segera.')
                    ->pause(1000)
                    ->click('button[type="submit"]:not([name="save_draft"])')
                    ->pause(2000)
                    ->assertSee('Rincian berhasil dikirim ke Admin');
        });

        // 8. Admin Melakukan Publikasi Langsung (Happy Path - Tanpa Revisi)
        $this->browse(function (Browser $browser) use ($admin, $proyek) {
            $browser->logout();
            $browser->loginAs($admin)
                    ->visit('/admin/proyek/' . $proyek->id . '/publikasi')
                    ->pause(1000)
                    ->press('Publikasikan Sekarang')
                    ->pause(1000)
                    // Modal Publish Confirm
                    ->script("
                        var form = document.querySelector('form[action$=\"/publish\"]');
                        if(form) form.submit();
                    ");
            
            $browser->pause(2000)
                    ->assertPathIs('/admin/proyek/kelola');
        });
        
        // Validasi DB Publikasi
        $this->assertDatabaseHas('proyeks', [
            'id' => $proyek->id,
            'status' => 'aktif_funding'
        ]);
    }

    /**
     * @group PublikasiProyek
     */
    public function test_modal_publikasi_muncul(): void
    {
        $admin = $this->createAdmin();
        $proyek = $this->createProject([
            'admin' => $admin,
            'status' => 'aktif_funding'
        ]);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/proyek/kelola')
                    ->click('button[dusk="btn-batalkan-publikasi"]') // In this context it opens modal with Mulai Eksekusi?
                    ->waitFor('#publishModal')
                    ->assertSee('Mulai Eksekusi?');
        });
    }

    /**
     * @group PublikasiProyek
     */
    public function test_tombol_publikasi_disabled_proyek_belum_lengkap(): void
    {
        $admin = $this->createAdmin();
        $proyek = $this->createProject([
            'admin' => $admin,
            'status' => 'menunggu_review_admin',
            'target_dana' => 0, // Incomplete
        ]);
        $vendorUser = $this->createVendorUser();
        \App\Models\PenugasanProyek::create([
            'id_proyek' => $proyek->id,
            'id_penyedia' => $vendorUser->penyedia_id,
        ]);

        $this->browse(function (Browser $browser) use ($admin, $proyek) {
            $browser->loginAs($admin)
                    ->visit('/admin/proyek/' . $proyek->id . '/publikasi')
                    ->pause(1000)
                    ->assertSee('Harap lengkapi semua data')
                    ->assertVisible('button[disabled]');
        });
    }

    /**
     * @group PublikasiProyek
     */
    public function test_proyek_draft_tidak_bisa_publish_paksa(): void
    {
        $admin = $this->createAdmin();
        $proyek = $this->createProject([
            'admin' => $admin,
            'status' => 'draft',
            'target_dana' => 0, // Incomplete
        ]);

        $this->browse(function (Browser $browser) use ($admin, $proyek) {
            $browser->loginAs($admin)
                    ->visit('/admin/proyek/' . $proyek->id . '/publikasi')
                    ->pause(1000)
                    ->script("
                        var btn = document.querySelector('#publishForm button');
                        if(btn) btn.removeAttribute('disabled');
                        var form = document.getElementById('publishForm');
                        if(form) form.submit();
                    ");
            
            $browser->pause(1000)
                    ->assertSee('Proyek belum dapat dipublikasikan. Detail teknis belum lengkap.');
        });
    }

    /**
     * @group PublikasiProyek
     */
    public function test_e2e_publikasi_dengan_revisi_hingga_tayang(): void
    {
        // 1. Persiapan Data Awal
        $admin = $this->createAdmin();
        $vendorUser = $this->createVendorUser();

        $proyek = $this->createProject([
            'admin' => $admin,
            'status' => 'menunggu_review_admin',
            'judul' => 'Proyek E2E Revisi Hingga Tayang',
            'penyedia_id' => $vendorUser->penyedia_id,
        ]);
        \App\Models\ProyekFoto::create([
            'proyek_id' => $proyek->id,
            'path' => 'proyek_fotos/dummy.jpg',
            'urutan' => 1
        ]);
        
        $penugasan = \App\Models\PenugasanProyek::create([
            'id_proyek' => $proyek->id,
            'id_penyedia' => $vendorUser->penyedia_id,
            'status_penugasan' => 'diterima',
            'tanggal_respon' => now(),
        ]);
        
        // Vendor sebelumnya sudah submit rincian
        \App\Models\DetailProyekVendor::create([
            'id_penugasan' => $penugasan->id_penugasan,
            'kapasitas_daya' => 10,
            'satuan_daya' => 'kW',
            'target_dana' => 10000000,
            'rincian_anggaran' => json_encode([['item' => 'Panel', 'harga' => 10000000]]),
            'status' => 'submitted'
        ]);

        // 2. Admin Review & Kembalikan ke Penyedia
        $this->browse(function (Browser $browser) use ($admin, $proyek) {
            $browser->logout();
            $browser->loginAs($admin)
                    ->visit('/admin/proyek/' . $proyek->id . '/publikasi')
                    ->pause(1000)
                    ->press('Kembalikan ke Penyedia')
                    ->waitFor('#modalKembalikan')
                    ->type('catatan_revisi', 'Kapasitas daya tolong dinaikkan menjadi 15 kW.')
                    ->press('Kirim Revisi')
                    ->assertPathIs('/admin/proyek/kelola')
                    ->waitForText('Proyek dikembalikan ke penyedia untuk direvisi.');
        });

        // 3. Vendor Melakukan Revisi & Submit Ulang via UI
        $this->browse(function (Browser $browser) use ($vendorUser, $penugasan) {
            $browser->logout()
                    ->loginAs($vendorUser)
                    ->visitRoute('vendor.proyek.show', $penugasan->id_penugasan)
                    ->pause(1500)
                    ->assertSee('Revisi Diperlukan dari Admin')
                    ->assertSee('Kapasitas daya tolong dinaikkan menjadi 15 kW')
                    ->clear('kapasitas_daya')
                    ->type('kapasitas_daya', '15')
                    ->select('satuan_daya', 'kW')
                    ->clear('target_dana')
                    ->type('target_dana', '15000000')
                    ->clear('cost_breakdown[0][nama]')
                    ->type('cost_breakdown[0][nama]', 'Panel Surya 15kWp')
                    ->clear('cost_breakdown[0][nominal]')
                    ->type('cost_breakdown[0][nominal]', '15000000')
                    ->pause(1000)
                    ->click('button[type="submit"]:not([name="save_draft"])')
                    ->pause(2000)
                    ->assertSee('Rincian berhasil dikirim ke Admin');
        });

        // 4. Admin Review Ulang & Publikasi
        $this->browse(function (Browser $browser) use ($admin, $proyek) {
            $browser->logout()
                    ->loginAs($admin)
                    ->visit('/admin/proyek/' . $proyek->id . '/publikasi')
                    ->pause(2000)
                    ->pause(2000)
                    ->script("
                        var form = document.getElementById('publishForm');
                        if(form) form.submit();
                    ");
            $browser->pause(2000);
        });

        $this->assertDatabaseHas('proyeks', [
            'id' => $proyek->id,
            'status' => 'aktif_funding'
        ]);

        // 5. Cek di Halaman Publik
        $this->browse(function (Browser $browser) use ($proyek) {
            $browser->logout()
                    ->visit('/')
                    ->assertSee('Proyek E2E Revisi Hingga Tayang');
        });
    }
}
