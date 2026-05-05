<?php

namespace Tests\Feature;

use App\Models\Desa;
use App\Models\PenyediaEnergi;
use App\Models\Proyek;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProyekKelolaPagesTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    private function makeDesa(): Desa
    {
        return Desa::create([
            'nama_desa'         => 'Desa Test',
            'provinsi'          => 'Jawa Barat',
            'kabupaten'         => 'Bandung',
            'kondisi_desa'      => 'off-grid',
            'sumber'            => 'manual',
            'status_verifikasi' => 'terverifikasi',
        ]);
    }

    private function makePenyedia(): PenyediaEnergi
    {
        return PenyediaEnergi::create([
            'nama'             => 'PT Energi Test',
            'spesialisasi'     => 'panel_surya',
            'provinsi_operasi' => 'Jawa Barat',
            'kisaran_harga_min' => 10000000,
            'kisaran_harga_max' => 50000000,
            'status'           => 'aktif',
        ]);
    }

    private function makeProyek(array $overrides = []): Proyek
    {
        $desa     = $this->makeDesa();
        $penyedia = $this->makePenyedia();

        return Proyek::create(array_merge([
            'judul'        => 'Proyek Test',
            'desa_id'      => $desa->id_desa,
            'penyedia_id'  => $penyedia->id,
            'jenis_energi' => 'panel_surya',
            'status'       => 'draft',
            'created_by'   => $this->admin->id,
        ], $overrides));
    }

    public function test_kirim_ke_penyedia_redirects_to_kelola(): void
    {
        $proyek = $this->makeProyek();

        $response = $this->actingAs($this->admin)
            ->post(route('proyek.kirim', $proyek->id));

        $response->assertRedirect(route('proyek.kelola'));
        $this->assertDatabaseHas('proyeks', [
            'id'     => $proyek->id,
            'status' => 'menunggu_konfirmasi_penyedia',
        ]);
    }

    public function test_kelola_page_loads_for_authenticated_user(): void
    {
        $response = $this->actingAs($this->admin)->get(route('proyek.kelola'));
        $response->assertStatus(200);
    }

    public function test_kelola_page_filters_by_search(): void
    {
        $desa = $this->makeDesa();
        Proyek::create([
            'judul'        => 'Panel Surya Desa Maju',
            'desa_id'      => $desa->id_desa,
            'jenis_energi' => 'panel_surya',
            'status'       => 'draft',
            'created_by'   => $this->admin->id,
        ]);
        Proyek::create([
            'judul'        => 'Biogas Lembah Hijau',
            'desa_id'      => $desa->id_desa,
            'jenis_energi' => 'biogas',
            'status'       => 'draft',
            'created_by'   => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('proyek.kelola', ['search' => 'Panel Surya']));

        $response->assertStatus(200);
        $response->assertViewHas('proyeks', fn ($proyeks) =>
            $proyeks->total() === 1 &&
            $proyeks->first()->judul === 'Panel Surya Desa Maju'
        );
    }

    public function test_kelola_page_filters_by_jenis_energi(): void
    {
        $desa = $this->makeDesa();
        Proyek::create([
            'judul' => 'Proyek A', 'desa_id' => $desa->id_desa,
            'jenis_energi' => 'panel_surya', 'status' => 'draft',
            'created_by' => $this->admin->id,
        ]);
        Proyek::create([
            'judul' => 'Proyek B', 'desa_id' => $desa->id_desa,
            'jenis_energi' => 'biogas', 'status' => 'draft',
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('proyek.kelola', ['jenis_energi' => 'biogas']));

        $response->assertStatus(200);
        $response->assertViewHas('proyeks', fn ($proyeks) =>
            $proyeks->total() === 1 &&
            $proyeks->first()->jenis_energi === 'biogas'
        );
    }

    public function test_publish_sets_aktif_funding_when_draft(): void
    {
        $proyek = $this->makeProyek(['status' => 'draft']);

        $response = $this->actingAs($this->admin)
            ->patch(route('proyek.publish', $proyek->id));

        $response->assertRedirect(route('proyek.kelola'));
        $this->assertDatabaseHas('proyeks', ['id' => $proyek->id, 'status' => 'aktif_funding']);
    }

    public function test_publish_sets_draft_when_aktif_funding(): void
    {
        $proyek = $this->makeProyek(['status' => 'aktif_funding']);

        $response = $this->actingAs($this->admin)
            ->patch(route('proyek.publish', $proyek->id));

        $response->assertRedirect(route('proyek.kelola'));
        $this->assertDatabaseHas('proyeks', ['id' => $proyek->id, 'status' => 'draft']);
    }

    public function test_publish_forbidden_when_eksekusi(): void
    {
        $proyek = $this->makeProyek(['status' => 'eksekusi']);

        $response = $this->actingAs($this->admin)
            ->patch(route('proyek.publish', $proyek->id));

        $response->assertStatus(403);
        $this->assertDatabaseHas('proyeks', ['id' => $proyek->id, 'status' => 'eksekusi']);
    }

    public function test_publish_forbidden_when_selesai(): void
    {
        $proyek = $this->makeProyek(['status' => 'selesai']);

        $response = $this->actingAs($this->admin)
            ->patch(route('proyek.publish', $proyek->id));

        $response->assertStatus(403);
    }

    public function test_destroy_deletes_draft_proyek(): void
    {
        $proyek = $this->makeProyek(['status' => 'draft']);

        $response = $this->actingAs($this->admin)
            ->delete(route('proyek.destroy', $proyek->id));

        $response->assertRedirect(route('proyek.kelola'));
        $this->assertDatabaseMissing('proyeks', ['id' => $proyek->id]);
    }

    public function test_destroy_forbidden_when_not_draft(): void
    {
        $proyek = $this->makeProyek(['status' => 'menunggu_konfirmasi_penyedia']);

        $response = $this->actingAs($this->admin)
            ->delete(route('proyek.destroy', $proyek->id));

        $response->assertStatus(403);
        $this->assertDatabaseHas('proyeks', ['id' => $proyek->id]);
    }
}
