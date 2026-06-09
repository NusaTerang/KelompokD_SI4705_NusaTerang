<?php

namespace App\Services;

use App\Models\Donasi;
use App\Models\Proyek;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class RefundService
{
    public function __construct(private SaldoService $saldo)
    {
    }

    /**
     * Donasi sukses milik donatur pada proyek yang belum diproses (refund/ikhlas).
     */
    public function refundableDonations(User $user, Proyek $proyek): Collection
    {
        return Donasi::where('id_proyek', $proyek->id)
            ->where('id_donatur', $user->getKey())
            ->where('status', 'success')
            ->where('refund_status', Donasi::REFUND_NONE)
            ->get();
    }

    /**
     * Total nominal yang akan dikembalikan ke saldo (berdasarkan rasio sisa dana).
     */
    public function refundableAmount(User $user, Proyek $proyek): float
    {
        return round(
            $this->refundableDonations($user, $proyek)
                ->sum(fn (Donasi $d) => $proyek->refundAmountFor((float) $d->nominal)),
            2
        );
    }

    /**
     * Proses refund: kembalikan sisa dana donatur ke saldo, tandai donasi.
     * @return float total yang dikembalikan
     */
    public function processRefund(User $user, Proyek $proyek): float
    {
        if (! $proyek->isRefundEligible()) {
            throw new RuntimeException('Proyek ini belum memenuhi syarat refund.');
        }

        return DB::transaction(function () use ($user, $proyek) {
            $donations = $this->refundableDonations($user, $proyek);

            if ($donations->isEmpty()) {
                throw new RuntimeException('Tidak ada donasi yang dapat direfund pada proyek ini.');
            }

            $total = 0.0;
            foreach ($donations as $donasi) {
                $amount = $proyek->refundAmountFor((float) $donasi->nominal);
                $donasi->update([
                    'refund_status' => Donasi::REFUND_REFUNDED,
                    'refund_amount' => $amount,
                    'refunded_at'   => now(),
                ]);
                $total += $amount;
            }

            $total = round($total, 2);

            if ($total <= 0) {
                throw new RuntimeException('Dana donasi sudah terpakai penuh, tidak ada yang dapat direfund.');
            }

            $this->saldo->credit(
                $user,
                $total,
                'refund',
                'Refund proyek: ' . $proyek->judul,
                $proyek->id
            );

            return $total;
        });
    }

    /**
     * Donatur merelakan (ikhlas) donasinya — tidak ada pengembalian saldo.
     * @return int jumlah donasi yang ditandai ikhlas
     */
    public function processIkhlas(User $user, Proyek $proyek): int
    {
        if (! $proyek->isRefundEligible()) {
            throw new RuntimeException('Proyek ini belum memenuhi syarat refund.');
        }

        $donations = $this->refundableDonations($user, $proyek);

        if ($donations->isEmpty()) {
            throw new RuntimeException('Tidak ada donasi yang dapat diproses pada proyek ini.');
        }

        foreach ($donations as $donasi) {
            $donasi->update([
                'refund_status' => Donasi::REFUND_IKHLAS,
                'refund_amount' => 0,
                'refunded_at'   => now(),
            ]);
        }

        return $donations->count();
    }
}
