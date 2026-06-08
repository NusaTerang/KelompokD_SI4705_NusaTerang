<?php

namespace App\Http\Controllers;

use App\Models\MutasiSaldo;
use App\Services\SaldoService;

class SaldoController extends Controller
{
    /**
     * Halaman saldo donatur — menampilkan saldo aktif & riwayat mutasi.
     */
    public function index(SaldoService $saldoService)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $saldo = $saldoService->getSaldo($user->id_donatur);

        $mutasiList = MutasiSaldo::where('id_donatur', $user->id_donatur)
            ->with('proyek:id,judul')
            ->latest()
            ->paginate(10);

        return view('donatur.saldo.index', compact('saldo', 'mutasiList'));
    }
}
