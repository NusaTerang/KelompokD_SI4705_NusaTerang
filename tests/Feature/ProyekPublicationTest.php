<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Proyek;
use App\Models\Desa;
use App\Models\PenyediaEnergi;
use App\Models\PenugasanProyek;
use App\Models\ProyekFoto;
use App\Models\PenugasanDetail;

class ProyekPublicationTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        // Asumsi ada role admin atau middleware tertentu. Kita buat user admin.
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_cannot_access_preview_if_status_invalid()
    {
        $proyek = Proyek::factory()->create(['status' => 'eksekusi']);

        $response = $this->actingAs($this->admin)
                         ->get(route('proyek.previewPublish', $proyek->id));

        $response->assertRedirect(route('proyek.kelola'))
                 ->assertSessionHas('error');
    }

    public function test_can_access_preview_if_status_valid()
    {
        $proyek = Proyek::factory()->create(['status' => 'menunggu_review_admin']);

        $response = $this->actingAs($this->admin)
                         ->get(route('proyek.previewPublish', $proyek->id));

        $response->assertStatus(200)
                 ->assertViewIs('admin.proyek.publish');
    }

    public function test_cannot_publish_if_data_incomplete()
    {
        // Proyek tanpa penyedia_id dan jenis_energi (seperti kasus Wae Rebo tadi)
        $proyek = Proyek::factory()->create([
            'status' => 'draft',
            'penyedia_id' => null,
            'jenis_energi' => null,
            'target_dana' => 250000000
        ]);

        $response = $this->actingAs($this->admin)
                         ->patch(route('proyek.publish', $proyek->id));

        $response->assertRedirect()
                 ->assertSessionHas('error');
                 
        $this->assertDatabaseHas('proyeks', [
            'id' => $proyek->id,
            'status' => 'draft'
        ]);
    }

    public function test_can_publish_instantly_if_data_complete()
    {
        // Buat proyek dengan data lengkap 5/5
        $penyedia = PenyediaEnergi::factory()->create();
        $proyek = Proyek::factory()->create([
            'status' => 'draft',
            'judul' => 'Proyek Lengkap',
            'deskripsi' => 'Deskripsi',
            'jenis_energi' => 'panel_surya',
            'estimasi_mulai' => now(),
            'estimasi_selesai' => now()->addDays(30),
            'target_dana' => 100000000,
            'penyedia_id' => $penyedia->id,
        ]);
        ProyekFoto::factory()->create(['proyek_id' => $proyek->id]);

        $response = $this->actingAs($this->admin)
                         ->patch(route('proyek.publish', $proyek->id));

        $response->assertRedirect(route('proyek.kelola'))
                 ->assertSessionHas('success');
                 
        $this->assertDatabaseHas('proyeks', [
            'id' => $proyek->id,
            'status' => 'aktif_funding'
        ]);
    }

    public function test_can_schedule_publish()
    {
        $penyedia = PenyediaEnergi::factory()->create();
        $proyek = Proyek::factory()->create([
            'status' => 'draft',
            'judul' => 'Proyek Lengkap',
            'deskripsi' => 'Deskripsi',
            'jenis_energi' => 'panel_surya',
            'estimasi_mulai' => now(),
            'estimasi_selesai' => now()->addDays(30),
            'target_dana' => 100000000,
            'penyedia_id' => $penyedia->id,
        ]);
        ProyekFoto::factory()->create(['proyek_id' => $proyek->id]);

        $scheduleTime = now()->addDays(1)->format('Y-m-d\TH:i');

        $response = $this->actingAs($this->admin)
                         ->patch(route('proyek.publish', $proyek->id), [
                             'jadwal_publikasi' => $scheduleTime
                         ]);

        $response->assertRedirect(route('proyek.kelola'))
                 ->assertSessionHas('success');

        $this->assertDatabaseHas('proyeks', [
            'id' => $proyek->id,
            'status' => 'dijadwalkan',
            'jadwal_publikasi' => $scheduleTime . ':00'
        ]);
    }

    public function test_scheduled_publish_command_works()
    {
        // Buat proyek yang dijadwalkan di masa lalu
        $proyek = Proyek::factory()->create([
            'status' => 'dijadwalkan',
            'jadwal_publikasi' => now()->subMinutes(5)
        ]);

        // Buat proyek yang dijadwalkan di masa depan (tidak boleh terpengaruh)
        $futureProyek = Proyek::factory()->create([
            'status' => 'dijadwalkan',
            'jadwal_publikasi' => now()->addMinutes(5)
        ]);

        $this->artisan('proyek:publish-scheduled')
             ->expectsOutputToContain('Berhasil mempublikasikan 1 proyek yang dijadwalkan.')
             ->assertExitCode(0);

        $this->assertDatabaseHas('proyeks', [
            'id' => $proyek->id,
            'status' => 'aktif_funding',
            'jadwal_publikasi' => null
        ]);

        $this->assertDatabaseHas('proyeks', [
            'id' => $futureProyek->id,
            'status' => 'dijadwalkan'
        ]);
    }
}
