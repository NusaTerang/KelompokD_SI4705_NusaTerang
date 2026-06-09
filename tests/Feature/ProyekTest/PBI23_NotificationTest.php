<?php

namespace Tests\Feature;

use App\Models\Desa;
use App\Models\PenugasanProyek;
use App\Models\PenyediaEnergi;
use App\Models\ProgressProyekVendor;
use App\Models\Proyek;
use App\Models\User;
use App\Notifications\DetailProyekDiisi;
use App\Notifications\ProgressProyekDikirim;
use App\Notifications\ProyekDitugaskan;
use App\Notifications\ProyekSelesai;
use App\Notifications\TargetDanaTercapai;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PBI23_NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_validasi_trigger_event_notifikasi(): void
    {
        Notification::fake();

        // Event 1: Admin assigns project → vendor receives ProyekDitugaskan
        [$admin, $vendorUser, $proyek] = $this->createProjectReadyForAssignment();
        $this->actingAs($admin)->post(route('proyek.kirim', $proyek->id));
        Notification::assertSentTo($vendorUser, ProyekDitugaskan::class);

        // Event 2: Vendor fills project detail → admin receives DetailProyekDiisi
        [$admin2, $vendorUser2, , $penugasan] = $this->createAssignedProject();
        $this->actingAs($vendorUser2)->put(route('vendor.proyek.detail', $penugasan->id_penugasan), [
            'kapasitas_daya'  => 25,
            'satuan_daya'     => 'kWp',
            'target_dana'     => 150000000,
            'durasi_minggu'   => 8,
            'catatan_teknis'  => 'Siap dipasang.',
        ]);
        Notification::assertSentTo($admin2, DetailProyekDiisi::class);

        // Event 3: Funding target reached → donor receives TargetDanaTercapai
        [, , $proyek3] = $this->createProjectReadyForAssignment();
        $proyek3->update(['target_dana' => 150000000, 'dana_terkumpul' => 0]);
        [$donorA3] = $this->createDonorsForProject($proyek3);
        $proyek3->recordFunding(150000000);
        Notification::assertSentTo($donorA3, TargetDanaTercapai::class);

        // Event 4: Vendor submits progress → donors receive ProgressProyekDikirim, nonDonor does not
        [$adminP, $vendorUserP, $proyekP, $penugasanP] = $this->createAssignedProject(status: 'eksekusi');
        [$donorA, , $nonDonor] = $this->createDonorsForProject($proyekP);
        $this->actingAs($vendorUserP)->post(route('vendor.proyek.progress.store', $penugasanP->id_penugasan), [
            'persentase'      => 50,
            'deskripsi'       => 'Panel mulai terpasang.',
            'status_progress' => 'berjalan',
        ]);
        Notification::assertSentTo($donorA, ProgressProyekDikirim::class);
        Notification::assertNotSentTo($nonDonor, ProgressProyekDikirim::class);
    }

    public function test_validasi_alur_interaksi_ui_notifikasi(): void
    {
        $user = User::factory()->create(['role' => 'donatur']);
        $proyek = $this->createProject();

        $user->notify(new ProgressProyekDikirim($proyek));
        $user->notify(new ProgressProyekDikirim($proyek));
        $user->notify(new ProgressProyekDikirim($proyek));

        // GET notifications.index shows unread count badge
        $this->actingAs($user)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('3');

        // PATCH read first unread notification → redirect to project page
        $notification = $user->unreadNotifications()->first();
        $this->actingAs($user)
            ->patch(route('notifications.read', $notification->id))
            ->assertRedirect(route('proyek.show', $proyek->id));

        $this->assertSame(2, $user->fresh()->unreadNotifications()->count());

        // PATCH read-all → all notifications marked read
        $this->actingAs($user)
            ->patch(route('notifications.read-all'))
            ->assertRedirect(route('notifications.index'));

        $this->assertSame(0, $user->fresh()->unreadNotifications()->count());
    }

    public function test_validasi_isolasi_data_notifikasi(): void
    {
        $donaturA = User::factory()->create(['role' => 'donatur']);
        $donaturB = User::factory()->create(['role' => 'donatur']);

        $proyekB = $this->createProject(['judul' => 'Notifikasi Donatur B']);
        $donaturB->notify(new ProgressProyekDikirim($proyekB));

        $this->actingAs($donaturA)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertDontSee('Notifikasi Donatur B');
    }

    public function test_validasi_tampilan_kosong(): void
    {
        $user = User::factory()->create(['role' => 'donatur']);

        $this->actingAs($user)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('Tidak ada notifikasi');
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function createProjectReadyForAssignment(): array
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $penyedia = $this->createPenyedia();
        $vendorUser = User::factory()->create(['role' => 'penyedia', 'penyedia_id' => $penyedia->id]);
        $proyek = $this->createProject(['penyedia_id' => $penyedia->id, 'created_by' => $admin->id]);

        return [$admin, $vendorUser, $proyek, $penyedia];
    }

    private function createAssignedProject(string $status = 'menunggu_konfirmasi_penyedia'): array
    {
        [$admin, $vendorUser, $proyek, $penyedia] = $this->createProjectReadyForAssignment();
        $proyek->update(['status' => $status]);
        $penugasan = PenugasanProyek::create([
            'id_proyek'          => $proyek->id,
            'id_penyedia'        => $penyedia->id,
            'status_penugasan'   => 'diterima',
            'tanggal_respon'     => now(),
        ]);

        return [$admin, $vendorUser, $proyek, $penugasan, $penyedia];
    }

    private function createDonorsForProject(Proyek $proyek): array
    {
        $donorA   = User::factory()->create(['role' => 'donatur']);
        $donorB   = User::factory()->create(['role' => 'donatur']);
        $nonDonor = User::factory()->create(['role' => 'donatur']);

        DB::table('donasi')->insert([
            ['id_donatur' => $donorA->getKey(), 'id_proyek' => $proyek->id, 'nominal' => 100000, 'status' => 'success', 'created_at' => now(), 'updated_at' => now()],
            ['id_donatur' => $donorA->getKey(), 'id_proyek' => $proyek->id, 'nominal' => 50000,  'status' => 'success', 'created_at' => now(), 'updated_at' => now()],
            ['id_donatur' => $donorB->getKey(), 'id_proyek' => $proyek->id, 'nominal' => 75000,  'status' => 'success', 'created_at' => now(), 'updated_at' => now()],
        ]);

        return [$donorA, $donorB, $nonDonor];
    }

    private function createProject(array $overrides = []): Proyek
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $desa  = Desa::create([
            'nama_desa'     => 'Desa Notifikasi',
            'provinsi'      => 'Jawa Barat',
            'kabupaten'     => 'Bandung',
            'kondisi_desa'  => 'off-grid',
            'sumber'        => 'solar_panel',
            'id_admin'      => $admin->id,
        ]);

        return Proyek::create(array_merge([
            'desa_id'          => $desa->id_desa,
            'judul'            => 'Proyek Notifikasi',
            'deskripsi'        => 'Proyek untuk notifikasi.',
            'jenis_energi'     => 'panel_surya',
            'estimasi_mulai'   => now()->toDateString(),
            'estimasi_selesai' => now()->addWeeks(4)->toDateString(),
            'target_dana'      => 150000000,
            'dana_terkumpul'   => 0,
            'status'           => 'draft',
            'created_by'       => $admin->id,
        ], $overrides));
    }

    private function createPenyedia(): PenyediaEnergi
    {
        return PenyediaEnergi::create([
            'nama'               => 'Vendor Notifikasi',
            'spesialisasi'       => 'panel_surya',
            'provinsi_operasi'   => 'Jawa Barat',
            'kisaran_harga_min'  => 1000000,
            'kisaran_harga_max'  => 10000000,
            'status'             => 'aktif',
        ]);
    }
}
