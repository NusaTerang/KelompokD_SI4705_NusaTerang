<?php

namespace App\Services;

use App\Models\MutasiSaldo;
use App\Models\SaldoDonatur;
use Illuminate\Support\Facades\DB;

class SaldoService
{
    /**
     * Ambil atau buat record saldo untuk donatur.
     */
    public function getOrCreateSaldo(int $idDonatur): SaldoDonatur
    {
        return SaldoDonatur::firstOrCreate(
            ['id_donatur' => $idDonatur],
            ['saldo' => 0]
        );
    }

    /**
     * Ambil saldo saat ini.
     */
    public function getSaldo(int $idDonatur): float
    {
        $saldo = SaldoDonatur::where('id_donatur', $idDonatur)->value('saldo');

        return (float) ($saldo ?? 0);
    }

    /**
     * Cek apakah saldo cukup untuk nominal tertentu.
     */
    public function cukupUntuk(int $idDonatur, float $nominal): bool
    {
        return $this->getSaldo($idDonatur) >= $nominal;
    }

    /**
     * Credit (tambah) saldo.
     * Dipakai oleh: PBI-30 (refund), PBI-33 (top-up).
     *
     * @param  int         $idDonatur   ID donatur
     * @param  float       $nominal     Jumlah dana yang ditambahkan
     * @param  string      $tipe        Tipe mutasi: 'refund' atau 'topup'
     * @param  int|null    $proyekId    ID proyek terkait (nullable)
     * @param  string      $keterangan  Deskripsi mutasi
     * @return MutasiSaldo Record mutasi yang dibuat
     */
    public function credit(int $idDonatur, float $nominal, string $tipe, ?int $proyekId, string $keterangan): MutasiSaldo
    {
        return DB::transaction(function () use ($idDonatur, $nominal, $tipe, $proyekId, $keterangan) {
            // Lock row untuk mencegah race condition
            $saldoRecord = SaldoDonatur::lockForUpdate()->firstOrCreate(
                ['id_donatur' => $idDonatur],
                ['saldo' => 0]
            );

            $saldoSebelum = (float) $saldoRecord->saldo;
            $saldoSesudah = $saldoSebelum + $nominal;

            $saldoRecord->update(['saldo' => $saldoSesudah]);

            return MutasiSaldo::create([
                'id_donatur'          => $idDonatur,
                'tipe'                => $tipe,
                'nominal'             => $nominal,
                'saldo_sebelum'       => $saldoSebelum,
                'saldo_sesudah'       => $saldoSesudah,
                'referensi_proyek_id' => $proyekId,
                'keterangan'          => $keterangan,
            ]);
        });
    }

    /**
     * Debit (kurangi) saldo.
     * Dipakai oleh: PBI-32 (bayar donasi pakai saldo).
     *
     * @param  int         $idDonatur   ID donatur
     * @param  float       $nominal     Jumlah dana yang dikurangi
     * @param  string      $tipe        Tipe mutasi: 'donasi'
     * @param  int|null    $proyekId    ID proyek terkait (nullable)
     * @param  string      $keterangan  Deskripsi mutasi
     * @return MutasiSaldo Record mutasi yang dibuat
     *
     * @throws \RuntimeException Jika saldo tidak mencukupi
     */
    public function debit(int $idDonatur, float $nominal, string $tipe, ?int $proyekId, string $keterangan): MutasiSaldo
    {
        return DB::transaction(function () use ($idDonatur, $nominal, $tipe, $proyekId, $keterangan) {
            $saldoRecord = SaldoDonatur::lockForUpdate()
                ->where('id_donatur', $idDonatur)
                ->first();

            if (! $saldoRecord || (float) $saldoRecord->saldo < $nominal) {
                throw new \RuntimeException('Saldo tidak mencukupi.');
            }

            $saldoSebelum = (float) $saldoRecord->saldo;
            $saldoSesudah = $saldoSebelum - $nominal;

            $saldoRecord->update(['saldo' => $saldoSesudah]);

            return MutasiSaldo::create([
                'id_donatur'          => $idDonatur,
                'tipe'                => $tipe,
                'nominal'             => $nominal,
                'saldo_sebelum'       => $saldoSebelum,
                'saldo_sesudah'       => $saldoSesudah,
                'referensi_proyek_id' => $proyekId,
                'keterangan'          => $keterangan,
            ]);
        });
    }
}
