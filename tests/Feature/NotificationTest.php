<?php

namespace Tests\Feature;

use App\Models\Desa;
use App\Models\PenugasanProyek;
use App\Models\PenyediaEnergi;
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

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_vendor_receives_notification_when_project_is_assigned(): void
    {
        Notification::fake();
        [$admin, $vendorUser, $proyek] = $this->createProjectReadyForAssignment();

        $this->actingAs($admin)->post(route('proyek.kirim', $proyek->id));

        Notification::assertSentTo($vendorUser, ProyekDitugaskan::class);
    }

    public function test_admin_receives_notification_when_vendor_submits_detail(): void
    {
        Notification::fake();
        [$admin, $vendorUser, , $penugasan] = $this->createAssignedProject();

        $this->actingAs($vendorUser)->put(route('vendor.proyek.detail', $penugasan->id_penugasan), [
            'kapasitas_daya' => 25,
            'satuan_daya' => 'kWp',
            'target_dana' => 150000000,
            'durasi_minggu' => 8,
            'catatan_teknis' => 'Siap dipasang.',
        ]);

        Notification::assertSentTo($admin, DetailProyekDiisi::class);
    }

    public function test_admin_and_project_donors_receive_notification_when_funding_target_is_reached(): void
    {
        Notification::fake();
        [$admin, , $proyek] = $this->createProjectReadyForAssignment();
        [$donorA, $donorB, $nonDonor] = $this->createDonorsForProject($proyek);

        $proyek->recordFunding(150000000);

        Notification::assertSentTo($admin, TargetDanaTercapai::class);
        Notification::assertSentTo($donorA, TargetDanaTercapai::class);
        Notification::assertSentTo($donorB, TargetDanaTercapai::class);
        Notification::assertNotSentTo($nonDonor, TargetDanaTercapai::class);
    }

    public function test_admin_and_project_donors_receive_notification_when_vendor_submits_progress(): void
    {
        Notification::fake();
        [$admin, $vendorUser, $proyek, $penugasan] = $this->createAssignedProject(status: 'eksekusi');
        [$donorA, $donorB, $nonDonor] = $this->createDonorsForProject($proyek);

        $this->actingAs($vendorUser)->post(route('vendor.proyek.progress.store', $penugasan->id_penugasan), [
            'persentase' => 50,
            'deskripsi' => 'Panel mulai terpasang.',
            'status_progress' => 'berjalan',
        ]);

        Notification::assertSentTo($admin, ProgressProyekDikirim::class);
        Notification::assertSentTo($donorA, ProgressProyekDikirim::class);
        Notification::assertSentTo($donorB, ProgressProyekDikirim::class);
        Notification::assertNotSentTo($nonDonor, ProgressProyekDikirim::class);
    }

    public function test_project_completion_notification_is_sent_instead_of_progress_notification(): void
    {
        Notification::fake();
        [$admin, $vendorUser, $proyek, $penugasan] = $this->createAssignedProject(status: 'eksekusi');
        [$donorA] = $this->createDonorsForProject($proyek);

        $this->actingAs($vendorUser)->post(route('vendor.proyek.progress.store', $penugasan->id_penugasan), [
            'persentase' => 100,
            'deskripsi' => 'Proyek selesai diuji.',
            'status_progress' => 'selesai',
        ]);

        Notification::assertSentTo($admin, ProyekSelesai::class);
        Notification::assertSentTo($donorA, ProyekSelesai::class);
        Notification::assertNotSentTo($admin, ProgressProyekDikirim::class);
    }

    public function test_notification_page_shows_unread_badge_and_empty_state(): void
    {
        $user = User::factory()->create(['role' => 'donatur']);
        $proyek = $this->createProject();
        $user->notify(new ProgressProyekDikirim($proyek));
        $user->notify(new ProgressProyekDikirim($proyek));
        $user->notify(new ProgressProyekDikirim($proyek));

        $this->actingAs($user)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('3');

        $newUser = User::factory()->create(['role' => 'donatur']);

        $this->actingAs($newUser)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('Tidak ada notifikasi');
    }

    public function test_admin_notification_page_uses_admin_layout(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('Kelola Data (Tabel)')
            ->assertDontSee('Tautan Cepat');
    }

    public function test_vendor_notification_page_uses_vendor_layout(): void
    {
        $penyedia = $this->createPenyedia();
        $vendor = User::factory()->create(['role' => 'penyedia', 'penyedia_id' => $penyedia->id]);

        $this->actingAs($vendor)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('Proyek Saya')
            ->assertDontSee('Tautan Cepat');
    }

    public function test_user_can_mark_notification_as_read_and_redirect_to_related_page(): void
    {
        $user = User::factory()->create(['role' => 'donatur']);
        $proyek = $this->createProject();
        $user->notify(new ProgressProyekDikirim($proyek));
        $notification = $user->notifications()->firstOrFail();

        $this->actingAs($user)
            ->patch(route('notifications.read', $notification->id))
            ->assertRedirect(route('proyek.show', $proyek->id));

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        $user = User::factory()->create(['role' => 'donatur']);
        $otherUser = User::factory()->create(['role' => 'donatur']);
        $proyek = $this->createProject();
        $user->notify(new ProgressProyekDikirim($proyek));
        $user->notify(new ProgressProyekDikirim($proyek));
        $otherUser->notify(new ProgressProyekDikirim($proyek));

        $this->actingAs($user)
            ->patch(route('notifications.read-all'))
            ->assertRedirect(route('notifications.index'));

        $this->assertSame(0, $user->fresh()->unreadNotifications()->count());
        $this->assertSame(1, $otherUser->fresh()->unreadNotifications()->count());
    }

    public function test_user_only_sees_own_notifications_newest_first(): void
    {
        $user = User::factory()->create(['role' => 'donatur']);
        $otherUser = User::factory()->create(['role' => 'donatur']);
        $oldProject = $this->createProject(['judul' => 'Notifikasi Lama']);
        $newProject = $this->createProject(['judul' => 'Notifikasi Baru']);
        $otherProject = $this->createProject(['judul' => 'Notifikasi Orang Lain']);

        $user->notify(new ProgressProyekDikirim($oldProject));
        $old = $user->notifications()->first();
        $old->forceFill(['created_at' => now()->subDay()])->save();
        $user->notify(new ProgressProyekDikirim($newProject));
        $otherUser->notify(new ProgressProyekDikirim($otherProject));

        $response = $this->actingAs($user)->get(route('notifications.index'));

        $response->assertOk();
        $response->assertSeeInOrder(['Notifikasi Baru', 'Notifikasi Lama']);
        $response->assertDontSee('Notifikasi Orang Lain');
    }

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
            'id_proyek' => $proyek->id,
            'id_penyedia' => $penyedia->id,
            'status_penugasan' => 'diterima',
            'tanggal_respon' => now(),
        ]);

        return [$admin, $vendorUser, $proyek, $penugasan, $penyedia];
    }

    private function createDonorsForProject(Proyek $proyek): array
    {
        $donorA = User::factory()->create(['role' => 'donatur']);
        $donorB = User::factory()->create(['role' => 'donatur']);
        $nonDonor = User::factory()->create(['role' => 'donatur']);

        DB::table('donasi')->insert([
            ['id_donatur' => $donorA->getKey(), 'id_proyek' => $proyek->id, 'nominal' => 100000, 'status' => 'berhasil', 'created_at' => now(), 'updated_at' => now()],
            ['id_donatur' => $donorA->getKey(), 'id_proyek' => $proyek->id, 'nominal' => 50000, 'status' => 'berhasil', 'created_at' => now(), 'updated_at' => now()],
            ['id_donatur' => $donorB->getKey(), 'id_proyek' => $proyek->id, 'nominal' => 75000, 'status' => 'berhasil', 'created_at' => now(), 'updated_at' => now()],
        ]);

        return [$donorA, $donorB, $nonDonor];
    }

    private function createProject(array $overrides = []): Proyek
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $desa = Desa::create([
            'nama_desa' => 'Desa Notifikasi',
            'provinsi' => 'Jawa Barat',
            'kabupaten' => 'Bandung',
            'kondisi_desa' => 'off-grid',
            'sumber' => 'solar_panel',
            'id_admin' => $admin->id,
        ]);

        return Proyek::create(array_merge([
            'desa_id' => $desa->id_desa,
            'judul' => 'Proyek Notifikasi',
            'deskripsi' => 'Proyek untuk notifikasi.',
            'jenis_energi' => 'panel_surya',
            'estimasi_mulai' => now()->toDateString(),
            'estimasi_selesai' => now()->addWeeks(4)->toDateString(),
            'target_dana' => 150000000,
            'dana_terkumpul' => 0,
            'status' => 'draft',
            'created_by' => $admin->id,
        ], $overrides));
    }

    private function createPenyedia(): PenyediaEnergi
    {
        return PenyediaEnergi::create([
            'nama' => 'Vendor Notifikasi',
            'spesialisasi' => 'panel_surya',
            'provinsi_operasi' => 'Jawa Barat',
            'kisaran_harga_min' => 1000000,
            'kisaran_harga_max' => 10000000,
            'status' => 'aktif',
        ]);
    }
}
