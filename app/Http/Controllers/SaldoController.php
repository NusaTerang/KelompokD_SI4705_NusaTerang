<?php

namespace App\Http\Controllers;

use App\Models\Proyek;
use App\Services\RefundService;

class SaldoController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $mutasi = $user->saldoMutasi()
            ->with('proyek')
            ->latest()
            ->paginate(15);

        return view('donatur.saldo', compact('user', 'mutasi'));
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
