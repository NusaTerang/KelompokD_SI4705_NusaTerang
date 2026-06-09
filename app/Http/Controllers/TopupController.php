<?php

namespace App\Http\Controllers;

use App\Models\Topup;
use App\Services\Midtrans\CheckStatusService;
use App\Services\Midtrans\CreateSnapTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TopupController extends Controller
{
    public function create()
    {
        return view('donatur.topup', ['user' => auth()->user()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|integer|min:10000|max:100000000',
        ], [
            'amount.required' => 'Nominal top up wajib diisi.',
            'amount.min'      => 'Minimum top up Rp 10.000.',
        ]);

        $topup = Topup::create([
            'user_id'        => auth()->id(),
            'number'         => 'TU-' . strtoupper(Str::random(10)),
            'amount'         => $validated['amount'],
            'payment_status' => Topup::STATUS_PENDING,
        ]);

        try {
            $snapToken = (new CreateSnapTokenService($topup))->getSnapToken();
            $topup->update(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            $topup->delete();
            return back()->withInput()->withErrors(['payment' => 'Gagal menghubungi gateway pembayaran: ' . $e->getMessage()]);
        }

        return redirect()->route('donatur.topup.show', $topup);
    }

    public function show(Topup $topup)
    {
        abort_unless($topup->user_id === auth()->id(), 403);

        if (! $topup->isPending()) {
            return redirect()->route('donatur.topup.status', $topup);
        }

        if (is_null($topup->snap_token)) {
            try {
                $snapToken = (new CreateSnapTokenService($topup))->getSnapToken();
                $topup->update(['snap_token' => $snapToken]);
            } catch (\Exception $e) {
                return back()->withErrors(['payment' => 'Gagal generate token pembayaran.']);
            }
        }

        return view('donatur.topup_pay', ['topup' => $topup, 'snapToken' => $topup->snap_token]);
    }

    public function status(Topup $topup)
    {
        abort_unless($topup->user_id === auth()->id(), 403);

        // Tarik status dari Midtrans (penting di localhost: webhook tak menjangkau server).
        if ($topup->isPending() && $topup->snap_token) {
            $this->syncStatusFromMidtrans($topup);
        }

        $topup->refresh();

        return view('donatur.topup_status', ['topup' => $topup]);
    }

    protected function syncStatusFromMidtrans(Topup $topup): void
    {
        try {
            $status = (new CheckStatusService)->getStatus($topup->number);
        } catch (\Exception $e) {
            return;
        }

        $transaction = $status->transaction_status ?? null;
        $fraud       = $status->fraud_status ?? 'accept';

        if (in_array($transaction, ['capture', 'settlement'], true) && $fraud === 'accept') {
            $topup->confirmPaid();
        } elseif ($transaction === 'expire') {
            $topup->update(['payment_status' => Topup::STATUS_EXPIRED]);
        } elseif (in_array($transaction, ['cancel', 'deny'], true)) {
            $topup->update(['payment_status' => Topup::STATUS_CANCELLED]);
        }
    }
}
