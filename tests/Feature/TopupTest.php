<?php

namespace Tests\Feature;

use App\Models\Topup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TopupTest extends TestCase
{
    use RefreshDatabase;

    private function makeTopup(User $user, int $amount = 100000): Topup
    {
        return Topup::create([
            'user_id'        => $user->getKey(),
            'number'         => 'TU-' . strtoupper(uniqid()),
            'amount'         => $amount,
            'payment_status' => Topup::STATUS_PENDING,
        ]);
    }

    public function test_confirm_paid_credits_saldo_and_records_mutation(): void
    {
        $user = User::factory()->create(['role' => 'donatur', 'saldo' => 25000]);
        $topup = $this->makeTopup($user, 100000);

        $topup->confirmPaid();

        $this->assertSame(Topup::STATUS_SUCCESS, $topup->fresh()->payment_status);
        $this->assertSame(125000.0, (float) $user->fresh()->saldo);
        $this->assertDatabaseHas('saldo_mutasi', [
            'id_donatur' => $user->getKey(),
            'tipe'       => 'topup',
            'nominal'    => 100000,
        ]);
    }

    public function test_confirm_paid_is_idempotent(): void
    {
        $user = User::factory()->create(['role' => 'donatur', 'saldo' => 0]);
        $topup = $this->makeTopup($user, 100000);

        $topup->confirmPaid();
        $topup->confirmPaid(); // panggil lagi

        $this->assertSame(100000.0, (float) $user->fresh()->saldo);
        $this->assertSame(1, \App\Models\SaldoMutasi::where('id_donatur', $user->getKey())->where('tipe', 'topup')->count());
    }

    public function test_topup_create_page_renders(): void
    {
        $user = User::factory()->create(['role' => 'donatur', 'saldo' => 50000]);

        $this->actingAs($user)
            ->get(route('donatur.topup.create'))
            ->assertOk()
            ->assertSee('Top Up Saldo')
            ->assertSee('Lanjut Bayar QRIS');
    }

    public function test_topup_status_page_shows_pending(): void
    {
        $user = User::factory()->create(['role' => 'donatur', 'saldo' => 0]);
        $topup = $this->makeTopup($user, 75000);

        // Tanpa snap_token -> tidak memanggil Midtrans, tetap pending.
        $this->actingAs($user)
            ->get(route('donatur.topup.status', $topup))
            ->assertOk()
            ->assertSee('Menunggu Pembayaran');
    }

    public function test_other_user_cannot_view_topup(): void
    {
        $owner = User::factory()->create(['role' => 'donatur']);
        $other = User::factory()->create(['role' => 'donatur']);
        $topup = $this->makeTopup($owner, 50000);

        $this->actingAs($other)
            ->get(route('donatur.topup.status', $topup))
            ->assertForbidden();
    }
}
