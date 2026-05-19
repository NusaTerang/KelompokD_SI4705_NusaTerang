<?php

namespace Tests\Feature;

use App\Models\Desa;
use App\Models\DetailProyekVendor;
use App\Models\PenugasanProyek;
use App\Models\PenyediaEnergi;
use App\Models\Proyek;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorProjectDetailEnergyTest extends TestCase
{
    use RefreshDatabase;

    public function test_vendor_detail_form_shows_project_energy_as_readonly(): void
    {
        [$user, $penugasan] = $this->createAssignedProject('biogas');

        $response = $this->actingAs($user)
            ->get(route('vendor.proyek.show', $penugasan->id_penugasan));

        $response->assertOk();
        $response->assertSee('Jenis Energi Utama');
        $response->assertSee('Biogas');
        $response->assertDontSee('name="jenis_energi[]"', false);
        $response->assertDontSee('Pilih Jenis Energi Utama');
    }

    public function test_save_uses_project_energy_not_request_energy(): void
    {
        [$user, $penugasan] = $this->createAssignedProject('panel_surya');

        $response = $this->actingAs($user)
            ->put(route('vendor.proyek.detail', $penugasan->id_penugasan), [
                'jenis_energi' => ['biogas'],
                'kapasitas_daya' => 12.5,
                'satuan_daya' => 'kWp',
                'target_dana' => 150000000,
                'cost_breakdown' => [
                    ['nama' => 'Panel dan inverter', 'nominal' => 100000000],
                    ['nama' => 'Instalasi', 'nominal' => 50000000],
                ],
                'durasi_minggu' => 8,
                'catatan_teknis' => 'Menggunakan konfigurasi sesuai kebutuhan proyek.',
            ]);

        $response->assertRedirect(route('vendor.proyek.show', $penugasan->id_penugasan));

        $this->assertDatabaseHas('detail_proyek_vendor', [
            'id_penugasan' => $penugasan->id_penugasan,
            'kapasitas_daya' => 12.5,
            'satuan_daya' => 'kWp',
            'target_dana' => 150000000,
            'durasi_minggu' => 8,
            'catatan_teknis' => 'Menggunakan konfigurasi sesuai kebutuhan proyek.',
            'status' => 'submitted',
        ]);

        $detail = DetailProyekVendor::where('id_penugasan', $penugasan->id_penugasan)->firstOrFail();
        $this->assertSame(['panel_surya'], $detail->jenis_energi);
    }

    private function createAssignedProject(string $jenisEnergi): array
    {
        $penyedia = PenyediaEnergi::create([
            'nama' => 'Vendor Energi Test',
            'spesialisasi' => $jenisEnergi,
            'provinsi_operasi' => 'Jawa Barat',
            'kisaran_harga_min' => 1000000,
            'kisaran_harga_max' => 10000000,
            'status' => 'aktif',
        ]);

        $vendorUser = User::factory()->create([
            'role' => 'penyedia',
            'penyedia_id' => $penyedia->id,
        ]);

        $adminUser = User::factory()->create([
            'role' => 'admin',
        ]);

        $desa = Desa::create([
            'nama_desa' => 'Desa Energi Test',
            'provinsi' => 'Jawa Barat',
            'kabupaten' => 'Bandung',
            'kondisi_desa' => 'off-grid',
            'sumber' => 'solar_panel',
            'id_admin' => $adminUser->id,
        ]);

        $proyek = Proyek::create([
            'desa_id' => $desa->id_desa,
            'penyedia_id' => $penyedia->id,
            'judul' => 'Proyek Energi Test',
            'deskripsi' => 'Kebutuhan energi desa untuk pengujian.',
            'jenis_energi' => $jenisEnergi,
            'estimasi_mulai' => now()->addWeek()->toDateString(),
            'estimasi_selesai' => now()->addWeeks(4)->toDateString(),
            'target_dana' => 150000000,
            'status' => 'diterima_penyedia',
            'created_by' => $adminUser->id,
        ]);

        $penugasan = PenugasanProyek::create([
            'id_proyek' => $proyek->id,
            'id_penyedia' => $penyedia->id,
            'status_penugasan' => 'diterima',
            'tanggal_respon' => now(),
        ]);

        return [$vendorUser, $penugasan, $proyek, $penyedia, $adminUser, $desa];
    }
}
