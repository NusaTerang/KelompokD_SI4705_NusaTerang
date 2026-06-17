<?php

namespace Tests\Feature;

use App\Models\Desa;
use App\Models\Donasi;
use App\Models\Order;
use App\Models\PenugasanProyek;
use App\Models\PenyediaEnergi;
use App\Models\Proyek;
use App\Models\User;
use App\Models\SaldoDonatur;
use App\Models\MutasiSaldo;
use App\Notifications\TargetDanaTercapai;
use App\Services\Midtrans\CreateSnapTokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class DonasiSaldoTest extends TestCase
{
    use RefreshDatabase;

    private function setupProyekAndUser(string $role = 'donatur', float $initialSaldo = 0): array
    {
        $desa = Desa::create([
            'nama_desa' => 'Desa Sukaramai',
            'provinsi' => 'Jawa Barat',
            'kabupaten' => 'Bandung',
            'kondisi_desa' => 'off-grid',
            'sumber' => 'solar_panel',
        ]);

        $user = User::create([
            'nama' => 'Test Donatur',
            'email' => 'donatur.test@example.com',
            'password' => bcrypt('password'),
            'role' => $role,
        ]);

        if ($initialSaldo > 0) {
            $user->saldoDonatur()->update([
                'saldo' => $initialSaldo,
            ]);
        }


        $proyek = Proyek::create([
            'desa_id' => $desa->id_desa,
            'judul' => 'PLTS Desa Mandiri',
            'deskripsi' => 'Deskripsi proyek PLTS.',
            'jenis_energi' => 'panel_surya',
            'estimasi_mulai' => now()->toDateString(),
            'estimasi_selesai' => now()->addDays(30)->toDateString(),
            'status' => 'aktif_funding',
            'target_dana' => 10000000,
            'dana_terkumpul' => 0,
        ]);

        return [$user, $proyek];
    }

    public function test_step_1_displays_active_balance(): void
    {
        [$user, $proyek] = $this->setupProyekAndUser('donatur', 150000);

        $response = $this->actingAs($user)
            ->get(route('donasi.create', ['proyek' => $proyek->id]));

        $response->assertOk();
        $response->assertSee('Saldo kamu: Rp 150.000');
    }

    public function test_step_2_auto_selects_qris_when_balance_is_zero(): void
    {
        [$user, $proyek] = $this->setupProyekAndUser('donatur', 0);

        $order = Order::create([
            'user_id' => $user->id_donatur,
            'proyek_id' => $proyek->id,
            'number' => 'NT-TEST1234',
            'total_price' => 50000,
            'donatur_name' => $user->name,
            'donatur_email' => $user->email,
            'payment_status' => Order::STATUS_PENDING,
        ]);

        // Mock Midtrans API
        Http::fake([
            'https://app.sandbox.midtrans.com/*' => Http::response(['token' => 'mock-snap-token'], 200),
        ]);

        $response = $this->actingAs($user)
            ->get(route('donasi.show', $order));

        $response->assertOk();
        $order->refresh();
        $this->assertEquals('qris', $order->payment_method);
        $this->assertEquals(50000, $order->amount_qris);
        $this->assertEquals(0, $order->amount_saldo);
        $response->assertSee('Scan & Bayar dengan QRIS', false);
    }

    public function test_step_2_displays_two_options_when_balance_is_sufficient(): void
    {
        [$user, $proyek] = $this->setupProyekAndUser('donatur', 100000);

        $order = Order::create([
            'user_id' => $user->id_donatur,
            'proyek_id' => $proyek->id,
            'number' => 'NT-TEST5678',
            'total_price' => 50000,
            'donatur_name' => $user->name,
            'donatur_email' => $user->email,
            'payment_status' => Order::STATUS_PENDING,
        ]);

        $response = $this->actingAs($user)
            ->get(route('donasi.show', $order));

        $response->assertOk();
        $response->assertSee('Pilih Metode Pembayaran');
        $response->assertSee('Bayar dengan Saldo');
        $response->assertSee('Bayar dengan QRIS');
        $response->assertDontSee('Gunakan Saldo + QRIS');
        $response->assertDontSee('Saldo Tidak Mencukupi');
    }

    public function test_step_2_displays_three_options_when_balance_is_insufficient_but_greater_than_zero(): void
    {
        [$user, $proyek] = $this->setupProyekAndUser('donatur', 20000);

        $order = Order::create([
            'user_id' => $user->id_donatur,
            'proyek_id' => $proyek->id,
            'number' => 'NT-TEST9012',
            'total_price' => 50000,
            'donatur_name' => $user->name,
            'donatur_email' => $user->email,
            'payment_status' => Order::STATUS_PENDING,
        ]);

        $response = $this->actingAs($user)
            ->get(route('donasi.show', $order));

        $response->assertOk();
        $response->assertSee('Pilih Metode Pembayaran');
        $response->assertSee('Saldo Tidak Mencukupi');
        $response->assertSee('Bayar dengan QRIS');
        $response->assertSee('Gunakan Saldo + QRIS');
        $response->assertSee('Saldo Terpakai:</span><strong>Rp 20.000', false);
        $response->assertSee('Kekurangan via QRIS:</span><strong>Rp 30.000', false);
    }

    public function test_paying_with_full_saldo(): void
    {
        [$user, $proyek] = $this->setupProyekAndUser('donatur', 100000);

        $order = Order::create([
            'user_id' => $user->id_donatur,
            'proyek_id' => $proyek->id,
            'number' => 'NT-SALDO100',
            'total_price' => 50000,
            'donatur_name' => $user->name,
            'donatur_email' => $user->email,
            'payment_status' => Order::STATUS_PENDING,
        ]);

        // Select method 'saldo'
        $response = $this->actingAs($user)
            ->post(route('donasi.select-method', $order), [
                'payment_method' => 'saldo',
            ]);

        $response->assertRedirect(route('donasi.show', $order));
        $order->refresh();
        $this->assertEquals('saldo', $order->payment_method);
        $this->assertEquals(50000, $order->amount_saldo);
        $this->assertEquals(0, $order->amount_qris);

        // Confirm payment
        $response = $this->actingAs($user)
            ->post(route('donasi.confirm-saldo', $order));

        $response->assertRedirect(route('donasi.status', $order));

        $order->refresh();
        $user->refresh();
        $proyek->refresh();

        $this->assertEquals(Order::STATUS_SUCCESS, $order->payment_status);
        $this->assertEquals(50000, $user->saldo);
        $this->assertEquals(50000, $proyek->dana_terkumpul);

        $this->assertDatabaseHas('donasi', [
            'id_proyek' => $proyek->id,
            'id_donatur' => $user->id_donatur,
            'nominal' => 50000,
            'status' => 'success',
        ]);

        $this->assertDatabaseHas('mutasi_saldo', [
            'id_donatur' => $user->id_donatur,
            'nominal' => 50000,
            'tipe' => 'keluar',
            'keterangan' => 'Donasi saldo untuk proyek: ' . $proyek->judul,
        ]);
    }

    public function test_paying_with_combination_payment_and_success_callback(): void
    {
        [$user, $proyek] = $this->setupProyekAndUser('donatur', 20000);

        $order = Order::create([
            'user_id' => $user->id_donatur,
            'proyek_id' => $proyek->id,
            'number' => 'NT-KOMBI100',
            'total_price' => 50000,
            'donatur_name' => $user->name,
            'donatur_email' => $user->email,
            'payment_status' => Order::STATUS_PENDING,
        ]);

        // Mock Midtrans API
        Http::fake([
            'https://app.sandbox.midtrans.com/*' => Http::response(['token' => 'mock-snap-token'], 200),
        ]);

        // Select combination method
        $response = $this->actingAs($user)
            ->post(route('donasi.select-method', $order), [
                'payment_method' => 'kombinasi',
            ]);

        $response->assertRedirect(route('donasi.show', $order));
        $order->refresh();
        $this->assertEquals('kombinasi', $order->payment_method);
        $this->assertEquals(20000, $order->amount_saldo);
        $this->assertEquals(30000, $order->amount_qris);

        // Balance should NOT be deducted yet
        $user->refresh();
        $this->assertEquals(20000, $user->saldo);

        // Simulate successful QRIS callback
        $signatureKey = hash('sha512', $order->number . '200' . '30000.00' . config('midtrans.server_key'));
        
        $callbackData = [
            'order_id' => $order->number,
            'status_code' => '200',
            'gross_amount' => '30000.00',
            'transaction_status' => 'settlement',
            'signature_key' => $signatureKey,
        ];

        // Midtrans callback uses unauthenticated post request
        $response = $this->postJson(route('midtrans.callback'), $callbackData);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $order->refresh();
        $user->refresh();
        $proyek->refresh();

        $this->assertEquals(Order::STATUS_SUCCESS, $order->payment_status);
        $this->assertEquals(0, $user->saldo);
        $this->assertEquals(50000, $proyek->dana_terkumpul);

        $this->assertDatabaseHas('donasi', [
            'id_proyek' => $proyek->id,
            'id_donatur' => $user->id_donatur,
            'nominal' => 50000,
            'status' => 'success',
        ]);

        $this->assertDatabaseHas('mutasi_saldo', [
            'id_donatur' => $user->id_donatur,
            'nominal' => 20000,
            'tipe' => 'keluar',
            'keterangan' => 'Pembayaran kombinasi donasi proyek: ' . $proyek->judul,
        ]);
    }

    public function test_confirm_paid_combination_deducts_saldo_once_and_keeps_target_notification(): void
    {
        Notification::fake();
        [$user, $proyek] = $this->setupProyekAndUser('donatur', 20000);
        $proyek->update(['target_dana' => 50000]);

        $order = Order::create([
            'user_id' => $user->id_donatur,
            'proyek_id' => $proyek->id,
            'number' => 'NT-KOMBI200',
            'total_price' => 50000,
            'donatur_name' => $user->name,
            'donatur_email' => $user->email,
            'payment_status' => Order::STATUS_PENDING,
            'payment_method' => 'kombinasi',
            'amount_saldo' => 20000,
            'amount_qris' => 30000,
        ]);

        $order->confirmPaid();
        $order->confirmPaid();

        $order->refresh();
        $user->refresh();
        $proyek->refresh();

        $this->assertEquals(Order::STATUS_SUCCESS, $order->payment_status);
        $this->assertEquals(0, $user->saldo);
        $this->assertEquals(50000, $proyek->dana_terkumpul);

        $this->assertDatabaseCount('donasi', 1);
        $this->assertDatabaseCount('mutasi_saldo', 1);
        $this->assertDatabaseHas('mutasi_saldo', [
            'id_donatur' => $user->id_donatur,
            'nominal' => 20000,
            'tipe' => 'keluar',
            'keterangan' => 'Pembayaran kombinasi donasi proyek: ' . $proyek->judul,
        ]);
        Notification::assertSentTo($user, TargetDanaTercapai::class);
    }

    public function test_automatic_refunds_on_vendor_project_cancellation(): void
    {
        [$user, $proyek] = $this->setupProyekAndUser('donatur', 0);

        // Add successful donation
        $donasi = Donasi::create([
            'id_proyek' => $proyek->id,
            'id_donatur' => $user->id_donatur,
            'nominal' => 75000,
            'status' => 'success',
        ]);

        // Setup vendor and assignment for project cancellation test
        $penyedia = PenyediaEnergi::create([
            'nama' => 'Vendor Test',
            'spesialisasi' => 'panel_surya',
            'provinsi_operasi' => 'Jawa Barat',
            'kisaran_harga_min' => 1000000,
            'kisaran_harga_max' => 10000000,
            'status' => 'aktif',
        ]);

        $vendorUser = User::create([
            'nama' => 'Test Vendor User',
            'email' => 'vendor.test@example.com',
            'password' => bcrypt('password'),
            'role' => 'penyedia',
            'penyedia_id' => $penyedia->id,
        ]);

        $proyek->update([
            'penyedia_id' => $penyedia->id,
            'status' => 'menunggu_keputusan_vendor',
            'expired_extension_pending' => true,
            'expired_original_end_date' => now()->subDay()->toDateString(),
            'expired_extended_at' => now(),
        ]);

        $penugasan = PenugasanProyek::create([
            'id_proyek' => $proyek->id,
            'id_penyedia' => $penyedia->id,
            'status_penugasan' => 'diterima',
        ]);

        // Vendor decides to refund
        $response = $this->actingAs($vendorUser)
            ->post(route('vendor.proyek.expiry-decision', $penugasan->id_penugasan), [
                'decision' => 'refund',
            ]);

        $response->assertRedirect(route('vendor.proyek.index'));
        
        $proyek->refresh();
        $user->refresh();
        $donasi->refresh();

        $this->assertSame('refund', $proyek->status);
        $this->assertEquals(75000, $user->saldo);
        $this->assertSame('refunded', $donasi->status);

        $this->assertDatabaseHas('mutasi_saldo', [
            'id_donatur' => $user->id_donatur,
            'nominal' => 75000,
            'tipe' => 'refund',
            'keterangan' => 'Refund donasi proyek: ' . $proyek->judul,
        ]);
    }

    public function test_proyek_show_displays_active_balance_and_payment_selector(): void
    {
        [$user, $proyek] = $this->setupProyekAndUser('donatur', 100000);

        $response = $this->actingAs($user)
            ->get(route('proyek.show', $proyek->id));

        $response->assertOk();
        $response->assertSee('Saldo kamu:');
        $response->assertSee('Metode Pembayaran');
        $response->assertSee('Bayar dengan QRIS');
        $response->assertSee('Bayar dengan Saldo');
    }

    public function test_proyek_show_qris_submission_preselects_qris_payment(): void
    {
        [$user, $proyek] = $this->setupProyekAndUser('donatur', 100000);

        $response = $this->actingAs($user)
            ->from(route('proyek.show', $proyek->id))
            ->post(route('donasi.store'), [
                'proyek_id' => $proyek->id,
                'donatur_name' => $user->name,
                'donatur_email' => $user->email,
                'donatur_phone' => '081234567890',
                'total_price' => 50000,
                'payment_method' => 'qris',
            ]);

        $response->assertSessionHasNoErrors();

        $order = Order::firstOrFail();

        $response->assertRedirect(route('donasi.show', $order));
        $this->assertSame('qris', $order->payment_method);
        $this->assertEquals(0, $order->amount_saldo);
        $this->assertEquals(50000, $order->amount_qris);
    }
}
