<?php

namespace Tests\Feature;

use App\Models\Desa;
use App\Models\Donasi;
use App\Models\Proyek;
use App\Models\User;
use App\Services\RefundService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaldoRefundTest extends TestCase
{
    use RefreshDatabase;

    private function createProyek(array $overrides = []): Proyek
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $desa = Desa::create([
            'nama_desa' => 'Desa Saldo',
            'provinsi' => 'Jawa Barat',
            'kabupaten' => 'Bandung',
            'kondisi_desa' => 'off-grid',
            'sumber' => 'solar_panel',
            'id_admin' => $admin->id,
        ]);

        return Proyek::create(array_merge([
            'desa_id' => $desa->id_desa,
            'judul' => 'Proyek Saldo',
            'deskripsi' => 'Proyek untuk pengujian saldo & refund.',
            'jenis_energi' => 'panel_surya',
            'estimasi_mulai' => now()->subWeeks(8)->toDateString(),
            'estimasi_selesai' => now()->subDay()->toDateString(),
            'target_dana' => 100000,
            'dana_terkumpul' => 100000,
            'dana_terpakai' => 50000,
            'status' => 'aktif_funding',
            'created_by' => $admin->id,
        ], $overrides));
    }

    private function donate(Proyek $proyek, User $user, int $nominal): Donasi
    {
        return Donasi::create([
            'id_proyek' => $proyek->id,
            'id_donatur' => $user->getKey(),
            'nominal' => $nominal,
            'status' => 'success',
        ]);
    }

    public function test_refund_credits_proportional_amount_to_saldo(): void
    {
        $proyek = $this->createProyek(); // ratio 50% (50rb terpakai dari 100rb)
        $donor = User::factory()->create(['role' => 'donatur', 'saldo' => 0]);
        $donasi = $this->donate($proyek, $donor, 100000);

        $total = app(RefundService::class)->processRefund($donor->fresh(), $proyek->fresh());

        $this->assertSame(50000.0, $total);
        $this->assertSame(50000.0, (float) $donor->fresh()->saldo);
        $this->assertSame(Donasi::REFUND_REFUNDED, $donasi->fresh()->refund_status);
        $this->assertSame(50000.0, (float) $donasi->fresh()->refund_amount);
        $this->assertDatabaseHas('saldo_mutasi', [
            'id_donatur' => $donor->getKey(),
            'tipe' => 'refund',
            'nominal' => 50000,
        ]);
    }

    public function test_refund_cannot_be_processed_twice(): void
    {
        $proyek = $this->createProyek();
        $donor = User::factory()->create(['role' => 'donatur', 'saldo' => 0]);
        $this->donate($proyek, $donor, 100000);

        app(RefundService::class)->processRefund($donor->fresh(), $proyek->fresh());

        $this->expectException(\RuntimeException::class);
        app(RefundService::class)->processRefund($donor->fresh(), $proyek->fresh());
    }

    public function test_ikhlas_marks_donation_without_crediting_saldo(): void
    {
        $proyek = $this->createProyek();
        $donor = User::factory()->create(['role' => 'donatur', 'saldo' => 0]);
        $donasi = $this->donate($proyek, $donor, 100000);

        app(RefundService::class)->processIkhlas($donor->fresh(), $proyek->fresh());

        $this->assertSame(0.0, (float) $donor->fresh()->saldo);
        $this->assertSame(Donasi::REFUND_IKHLAS, $donasi->fresh()->refund_status);
    }

    public function test_refund_unavailable_when_project_not_eligible(): void
    {
        $proyek = $this->createProyek([
            'estimasi_selesai' => now()->addWeeks(2)->toDateString(),
            'status' => 'aktif_funding',
        ]);
        $donor = User::factory()->create(['role' => 'donatur', 'saldo' => 0]);
        $this->donate($proyek, $donor, 100000);

        $this->assertFalse($proyek->isRefundEligible());

        $this->expectException(\RuntimeException::class);
        app(RefundService::class)->processRefund($donor->fresh(), $proyek->fresh());
    }

    public function test_fully_used_funds_yield_no_refund(): void
    {
        $proyek = $this->createProyek(['dana_terpakai' => 100000]); // ratio 0
        $donor = User::factory()->create(['role' => 'donatur', 'saldo' => 0]);
        $this->donate($proyek, $donor, 100000);

        $this->assertSame(0.0, $proyek->refundRatio());

        $this->expectException(\RuntimeException::class);
        app(RefundService::class)->processRefund($donor->fresh(), $proyek->fresh());
    }

    public function test_donation_paid_with_saldo_deducts_balance_and_funds_project(): void
    {
        $proyek = $this->createProyek([
            'estimasi_selesai' => now()->addWeeks(4)->toDateString(),
            'dana_terkumpul' => 0,
            'dana_terpakai' => 0,
            'target_dana' => 500000,
        ]);
        $donor = User::factory()->create(['role' => 'donatur', 'saldo' => 200000]);

        $response = $this->actingAs($donor)->post(route('donasi.store'), [
            'proyek_id' => $proyek->id,
            'donatur_name' => $donor->nama,
            'donatur_email' => $donor->email,
            'total_price' => 50000,
            'payment_method' => 'saldo',
        ]);

        $response->assertRedirect();
        $this->assertSame(150000.0, (float) $donor->fresh()->saldo);
        $this->assertSame(50000.0, (float) $proyek->fresh()->dana_terkumpul);
        $this->assertDatabaseHas('donasi', [
            'id_proyek' => $proyek->id,
            'id_donatur' => $donor->getKey(),
            'nominal' => 50000,
            'status' => 'success',
        ]);
        $this->assertDatabaseHas('saldo_mutasi', [
            'id_donatur' => $donor->getKey(),
            'tipe' => 'donasi',
            'nominal' => -50000,
        ]);
    }

    public function test_saldo_page_renders_for_donatur(): void
    {
        $donor = User::factory()->create(['role' => 'donatur', 'saldo' => 75000]);

        $this->actingAs($donor)
            ->get(route('donatur.saldo'))
            ->assertOk()
            ->assertSee('Saldo Saya')
            ->assertSee('Rp 75.000');
    }

    public function test_project_page_shows_refund_panel_for_eligible_donor(): void
    {
        $proyek = $this->createProyek(); // past deadline, ratio 50%
        $donor = User::factory()->create(['role' => 'donatur', 'saldo' => 0]);
        $this->donate($proyek, $donor, 100000);

        $this->actingAs($donor)
            ->get(route('proyek.show', $proyek->id))
            ->assertOk()
            ->assertSee('Donasi Anda')
            ->assertSee('Refund Rp 50.000 ke Saldo');
    }

    public function test_donation_with_insufficient_saldo_is_rejected(): void
    {
        $proyek = $this->createProyek([
            'estimasi_selesai' => now()->addWeeks(4)->toDateString(),
            'dana_terkumpul' => 0,
            'target_dana' => 500000,
        ]);
        $donor = User::factory()->create(['role' => 'donatur', 'saldo' => 10000]);

        $response = $this->actingAs($donor)->post(route('donasi.store'), [
            'proyek_id' => $proyek->id,
            'donatur_name' => $donor->nama,
            'donatur_email' => $donor->email,
            'total_price' => 50000,
            'payment_method' => 'saldo',
        ]);

        $response->assertSessionHasErrors('payment');
        $this->assertSame(10000.0, (float) $donor->fresh()->saldo);
        $this->assertSame(0.0, (float) $proyek->fresh()->dana_terkumpul);
    }
}
