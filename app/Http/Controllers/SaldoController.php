<?php

namespace App\Http\Controllers;

use App\Models\MutasiSaldo;
use App\Models\Proyek;
use App\Services\RefundService;
use App\Services\SaldoService;

class SaldoController extends Controller
{
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

    public function refund(Proyek $proyek, RefundService $refunds)
    {
        $user = auth()->user();

        try {
            $total = $refunds->processRefund($user, $proyek);
        } catch (\Throwable $e) {
            return back()->withErrors(['refund' => $e->getMessage()]);
        }

        return back()->with('success', 'Refund Rp ' . number_format($total, 0, ',', '.') . ' berhasil masuk ke saldo kamu.');
    }

    public function ikhlas(Proyek $proyek, RefundService $refunds)
    {
        $user = auth()->user();

        try {
            $refunds->processIkhlas($user, $proyek);
        } catch (\Throwable $e) {
            return back()->withErrors(['refund' => $e->getMessage()]);
        }

        return back()->with('success', 'Terima kasih, donasimu untuk proyek ini telah diikhlaskan. 🙏');
    }
}
