<?php

namespace App\Services;

use App\Models\SaldoMutasi;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SaldoService
{
    /**
     * Menambah saldo donatur dan mencatat mutasinya. Atomik.
     */
    public function credit(User $user, float $amount, string $tipe, ?string $keterangan = null, ?int $proyekId = null): SaldoMutasi
    {
        if ($amount <= 0) {
            throw new RuntimeException('Nominal kredit saldo harus lebih dari 0.');
        }

        return $this->record($user, $amount, $tipe, $keterangan, $proyekId);
    }

    /**
     * Mengurangi saldo donatur dan mencatat mutasinya. Atomik.
     * Melempar exception bila saldo tidak cukup.
     */
    public function debit(User $user, float $amount, string $tipe, ?string $keterangan = null, ?int $proyekId = null): SaldoMutasi
    {
        if ($amount <= 0) {
            throw new RuntimeException('Nominal debit saldo harus lebih dari 0.');
        }

        return $this->record($user, -$amount, $tipe, $keterangan, $proyekId);
    }

    /**
     * @param float $signedAmount positif = masuk, negatif = keluar
     */
    protected function record(User $user, float $signedAmount, string $tipe, ?string $keterangan, ?int $proyekId): SaldoMutasi
    {
        return DB::transaction(function () use ($user, $signedAmount, $tipe, $keterangan, $proyekId) {
            // Kunci baris user agar tidak ada balapan saldo.
            $fresh = User::whereKey($user->getKey())->lockForUpdate()->firstOrFail();

            $before = (float) $fresh->saldo;
            $after  = round($before + $signedAmount, 2);

            if ($after < 0) {
                throw new RuntimeException('Saldo tidak mencukupi.');
            }

            $fresh->update(['saldo' => $after]);
            $user->setAttribute('saldo', $after);

            return SaldoMutasi::create([
                'id_donatur'    => $fresh->getKey(),
                'tipe'          => $tipe,
                'nominal'       => round($signedAmount, 2),
                'saldo_sebelum' => $before,
                'saldo_sesudah' => $after,
                'keterangan'    => $keterangan,
                'proyek_id'     => $proyekId,
            ]);
        });
    }
}
