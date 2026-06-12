<?php

namespace Tests\Browser\ProyekTest;

use App\Models\LaporanAkhirProyekVendor;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\Browser\ProyekTest\Concerns\CreatesProyekFixtures;
use Tests\DuskTestCase;

class PBI25VendorFinalReportDuskTest extends DuskTestCase
{
    use CreatesProyekFixtures;
    use DatabaseTruncation;

    public function test_vendor_berhasil_submit_laporan_akhir(): void
    {
        [$vendorUser, $penugasan, $proyek] = $this->createCompletedProgressProject();

        $this->browse(function (Browser $browser) use ($vendorUser, $penugasan, $proyek) {
            $browser->loginAs($vendorUser)
                ->visitRoute('vendor.proyek.show', $penugasan->id_penugasan)
                ->assertSee('Form Laporan Akhir')
                ->type('deskripsi', 'Seluruh instalasi panel surya selesai.')
                ->type('kapasitas_terpasang', '15')
                ->select('satuan_kapasitas', 'kWp')
                ->attach('fotos[]', $this->imageFixturePath())
                ->type('catatan', 'Sistem siap digunakan warga.')
                ->press('SUBMIT LAPORAN AKHIR')
                ->waitForText('Laporan akhir sudah dikirim')
                ->visitRoute('proyek.show', $proyek->id)
                ->assertSee('Laporan Akhir')
                ->assertSee('Seluruh instalasi panel surya selesai.');
        });

        $this->assertDatabaseHas('laporan_akhir_proyek_vendor', [
            'id_penugasan' => $penugasan->id_penugasan,
            'id_proyek' => $proyek->id,
            'status' => 'submitted',
        ]);
        $this->assertDatabaseHas('proyeks', ['id' => $proyek->id, 'status' => 'selesai']);
    }

    public function test_vendor_simpan_draft_laporan_akhir(): void
    {
        [$vendorUser, $penugasan, $proyek] = $this->createCompletedProgressProject();

        $this->browse(function (Browser $browser) use ($vendorUser, $penugasan) {
            $browser->loginAs($vendorUser)
                ->visitRoute('vendor.proyek.show', $penugasan->id_penugasan)
                ->type('deskripsi', 'Instalasi selesai dan perangkat sudah diuji.')
                ->type('kapasitas_terpasang', '12.5')
                ->select('satuan_kapasitas', 'kWp')
                ->attach('fotos[]', $this->imageFixturePath())
                ->press('SIMPAN DRAFT')
                ->waitForText('Form Laporan Akhir');
        });

        $this->assertDatabaseHas('laporan_akhir_proyek_vendor', [
            'id_penugasan' => $penugasan->id_penugasan,
            'status' => 'draft',
        ]);
        $this->assertDatabaseHas('proyeks', ['id' => $proyek->id, 'status' => 'eksekusi']);
    }

    public function test_validasi_field_wajib(): void
    {
        [$vendorUser, $penugasan] = $this->createCompletedProgressProject();

        $this->browse(function (Browser $browser) use ($vendorUser, $penugasan) {
            $browser->loginAs($vendorUser)
                ->visitRoute('vendor.proyek.show', $penugasan->id_penugasan)
                ->press('SUBMIT LAPORAN AKHIR')
                ->waitForText('Deskripsi laporan wajib diisi')
                ->assertSee('Minimal 1 foto dokumentasi akhir wajib diunggah');
        });
    }

    public function test_validasi_pencegahan_duplikasi_laporan(): void
    {
        [$vendorUser, $penugasan] = $this->createCompletedProgressProject();

        LaporanAkhirProyekVendor::create([
            'id_penugasan' => $penugasan->id_penugasan,
            'id_proyek' => $penugasan->id_proyek,
            'id_penyedia' => $penugasan->id_penyedia,
            'deskripsi' => 'Laporan pertama.',
            'kapasitas_terpasang' => 10,
            'satuan_kapasitas' => 'kWp',
            'foto_paths' => ['laporan/pertama.svg'],
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $this->browse(function (Browser $browser) use ($vendorUser, $penugasan) {
            $browser->loginAs($vendorUser)
                ->visitRoute('vendor.proyek.show', $penugasan->id_penugasan)
                ->assertSee('Laporan akhir sudah dikirim.');
        });
    }

    public function test_validasi_hak_akses(): void
    {
        [, $penugasan] = $this->createCompletedProgressProject();
        $otherVendor = $this->createVendorUser($this->createPenyedia(['nama' => 'Vendor Lain']));
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function (Browser $browser) use ($otherVendor, $admin, $penugasan) {
            $browser->loginAs($otherVendor)
                ->visitRoute('vendor.proyek.show', $penugasan->id_penugasan)
                ->assertSee('404');

            $browser->loginAs($admin)
                ->visitRoute('vendor.proyek.show', $penugasan->id_penugasan)
                ->assertSee('403');
        });
    }
}
