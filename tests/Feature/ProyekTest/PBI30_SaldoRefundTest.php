<?php

namespace Tests\Feature;

use App\Events\ProyekDibatalkan;
use App\Models\{Desa, Donasi, MutasiSaldo, Proyek, SaldoDonatur, User};
use App\Notifications\{LaporanRefundAdmin, RefundDonaturNotification};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PBI30_SaldoRefundTest extends TestCase
{
    use RefreshDatabase;

    // ─── Helper ──────────────────────────────────────────────────────────────

    private function createCancelledProject(int $donorCount = 3): array
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $desa  = Desa::create([
            'nama_desa'    => 'Desa Refund',
            'provinsi'     => 'Jawa Barat',
            'kabupaten'    => 'Bandung',
            'kondisi_desa' => 'off-grid',
            'sumber'       => 'solar_panel',
            'id_admin'     => $admin->id,
        ]);
        $proyek = Proyek::create([
            'desa_id'          => $desa->id_desa,
            'judul'            => 'Proyek Refund Test',
            'deskripsi'        => 'Proyek untuk refund.',
            'jenis_energi'     => 'panel_surya',
            'estimasi_mulai'   => now()->subWeeks(8)->toDateString(),
            'estimasi_selesai' => now()->subDay()->toDateString(),
            'target_dana'      => 500000,
            'dana_terkumpul'   => 300000,
            'status'           => 'aktif_funding',
            'created_by'       => $admin->id,
        ]);

        $donors = [];
        for ($i = 0; $i < $donorCount; $i++) {
            // UserObserver automatically creates SaldoDonatur(saldo=0) on user creation
            $donor    = User::factory()->create(['role' => 'donatur']);
            $donors[] = $donor;
        }

        return [$proyek, $donors, $admin];
    }

    // ─── TC-01 ───────────────────────────────────────────────────────────────

    public function test_refund_otomatis_massal_sukses(): void
    {
        Notification::fake();

        [$proyek, $donors, $admin] = $this->createCancelledProject(3);
        [$donorA, $donorB, $donorC] = $donors;

        Donasi::create(['id_proyek' => $proyek->id, 'id_donatur' => $donorA->getKey(), 'nominal' => 100000, 'status' => 'success']);
        Donasi::create(['id_proyek' => $proyek->id, 'id_donatur' => $donorB->getKey(), 'nominal' => 75000,  'status' => 'success']);
        Donasi::create(['id_proyek' => $proyek->id, 'id_donatur' => $donorC->getKey(), 'nominal' => 125000, 'status' => 'success']);

        event(new ProyekDibatalkan($proyek));

        $this->assertSame(100000.0, (float) SaldoDonatur::where('id_donatur', $donorA->getKey())->value('saldo'));
        $this->assertSame(75000.0,  (float) SaldoDonatur::where('id_donatur', $donorB->getKey())->value('saldo'));
        $this->assertSame(125000.0, (float) SaldoDonatur::where('id_donatur', $donorC->getKey())->value('saldo'));

        $this->assertDatabaseHas('mutasi_saldo', [
            'id_donatur'          => $donorA->getKey(),
            'tipe'                => 'refund',
            'nominal'             => 100000,
            'referensi_proyek_id' => $proyek->id,
        ]);

        Notification::assertSentTo($donorA, RefundDonaturNotification::class);
        Notification::assertSentTo($donorB, RefundDonaturNotification::class);
        Notification::assertSentTo($donorC, RefundDonaturNotification::class);
        Notification::assertSentTo($admin,  LaporanRefundAdmin::class);
    }

    // ─── TC-02 ───────────────────────────────────────────────────────────────

    public function test_refund_multi_donation_satu_donatur(): void
    {
        [$proyek, $donors] = $this->createCancelledProject(1);
        [$donorA] = $donors;

        Donasi::create(['id_proyek' => $proyek->id, 'id_donatur' => $donorA->getKey(), 'nominal' => 50000, 'status' => 'success']);
        Donasi::create(['id_proyek' => $proyek->id, 'id_donatur' => $donorA->getKey(), 'nominal' => 75000, 'status' => 'success']);

        event(new ProyekDibatalkan($proyek));

        $this->assertSame(125000.0, (float) SaldoDonatur::where('id_donatur', $donorA->getKey())->value('saldo'));

        $this->assertDatabaseHas('mutasi_saldo', [
            'id_donatur'          => $donorA->getKey(),
            'tipe'                => 'refund',
            'nominal'             => 125000,
            'referensi_proyek_id' => $proyek->id,
        ]);
    }

    // ─── TC-03 ───────────────────────────────────────────────────────────────

    public function test_penanganan_kegagalan_refund_salah_satu_donatur(): void
    {
        Notification::fake();

        [$proyek, $donors, $admin] = $this->createCancelledProject(2);
        [$donorA, $donorB] = $donors;

        Donasi::create(['id_proyek' => $proyek->id, 'id_donatur' => $donorA->getKey(), 'nominal' => 50000, 'status' => 'success']);
        Donasi::create(['id_proyek' => $proyek->id, 'id_donatur' => $donorB->getKey(), 'nominal' => 60000, 'status' => 'success']);

        // Simulate donorB being removed — cascade deletes their saldo_donatur,
        // mutasi_saldo and donasi rows, leaving only donorA's donation for
        // the listener to process.
        $donorB->delete();

        event(new ProyekDibatalkan($proyek));

        // donorA's refund must succeed
        $this->assertSame(50000.0, (float) SaldoDonatur::where('id_donatur', $donorA->getKey())->value('saldo'));
        Notification::assertSentTo($donorA, RefundDonaturNotification::class);

        // Admin must receive a summary notification regardless
        Notification::assertSentTo($admin, LaporanRefundAdmin::class);
    }

    // ─── TC-04 ───────────────────────────────────────────────────────────────

    public function test_keamanan_proyek_aktif_tidak_di_refund(): void
    {
        [$proyek, $donors] = $this->createCancelledProject(1);
        [$donorA] = $donors;

        Donasi::create(['id_proyek' => $proyek->id, 'id_donatur' => $donorA->getKey(), 'nominal' => 100000, 'status' => 'success']);

        // Status change to 'eksekusi' must NOT fire ProyekDibatalkan
        $proyek->update(['status' => 'eksekusi']);

        $this->assertSame(0.0, (float) SaldoDonatur::where('id_donatur', $donorA->getKey())->value('saldo'));
        $this->assertDatabaseMissing('mutasi_saldo', [
            'id_donatur' => $donorA->getKey(),
            'tipe'       => 'refund',
        ]);
    }
}
