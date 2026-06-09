<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Proyek;
use App\Services\Midtrans\CheckStatusService;
use App\Services\Midtrans\CreateSnapTokenService;
use App\Services\SaldoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function create()
    {
        $user   = auth()->user();
        $proyek = request()->filled('proyek') ? Proyek::find(request('proyek')) : null;
        return view('donasi.create', compact('user', 'proyek'));
    }

    public function store(Request $request, SaldoService $saldo)
    {
        $validated = $request->validate([
            'proyek_id'      => 'required|exists:proyeks,id',
            'donatur_name'   => 'required|string|max:100',
            'donatur_email'  => 'required|email|max:150',
            'donatur_phone'  => 'nullable|string|max:20',
            'total_price'    => 'required|integer|min:10000|max:100000000',
            'pesan'          => 'nullable|string|max:500',
            'payment_method' => 'nullable|in:midtrans,saldo',
        ], [
            'proyek_id.required'     => 'Proyek tidak valid.',
            'donatur_name.required'  => 'Nama donatur wajib diisi.',
            'donatur_email.required' => 'Email wajib diisi.',
            'total_price.required'   => 'Jumlah donasi wajib diisi.',
            'total_price.min'        => 'Minimum donasi Rp 10.000.',
        ]);

        $user = auth()->user();
        $method = $validated['payment_method'] ?? 'midtrans';

        // Validasi saldo sebelum membuat order agar tidak perlu rollback.
        if ($method === 'saldo' && $user->saldo < $validated['total_price']) {
            return back()->withInput()->withErrors([
                'payment' => 'Saldo tidak mencukupi. Saldo kamu Rp ' . number_format($user->saldo, 0, ',', '.') . '.',
            ]);
        }

        $order = Order::create([
            'user_id'        => auth()->id(),
            'proyek_id'      => $validated['proyek_id'],
            'number'         => 'NT-' . strtoupper(Str::random(10)),
            'total_price'    => $validated['total_price'],
            'donatur_name'   => $validated['donatur_name'],
            'donatur_email'  => $validated['donatur_email'],
            'donatur_phone'  => $validated['donatur_phone'] ?? null,
            'pesan'          => $validated['pesan'] ?? null,
            'payment_status' => Order::STATUS_PENDING,
        ]);

        // ─── Pembayaran via Saldo ──────────────────────────────────────────
        if ($method === 'saldo') {
            try {
                DB::transaction(function () use ($saldo, $user, $order) {
                    $saldo->debit(
                        $user,
                        (float) $order->total_price,
                        'donasi',
                        'Donasi proyek: ' . ($order->proyek->judul ?? '-'),
                        $order->proyek_id
                    );
                    $order->confirmPaid();
                });
            } catch (\Throwable $e) {
                $order->delete();
                return back()->withInput()->withErrors(['payment' => 'Gagal memproses donasi via saldo: ' . $e->getMessage()]);
            }

            return redirect()->route('donasi.status', $order)
                ->with('success', 'Donasi via saldo berhasil! Terima kasih atas dukunganmu.');
        }

        // ─── Pembayaran via Midtrans ───────────────────────────────────────
        try {
            $midtrans  = new CreateSnapTokenService($order);
            $snapToken = $midtrans->getSnapToken();
            $order->update(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            $order->delete();
            return back()
                ->withInput()
                ->withErrors(['payment' => 'Gagal menghubungi gateway pembayaran: ' . $e->getMessage()]);
        }

        return redirect()->route('donasi.show', $order);
    }

    public function show(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        if (!$order->isPending()) {
            return redirect()->route('donasi.status', $order);
        }

        if (is_null($order->snap_token)) {
            try {
                $midtrans  = new CreateSnapTokenService($order);
                $snapToken = $midtrans->getSnapToken();
                $order->update(['snap_token' => $snapToken]);
            } catch (\Exception $e) {
                return back()->withErrors(['payment' => 'Gagal generate token pembayaran.']);
            }
        }

        $snapToken = $order->snap_token;

        return view('donasi.show', compact('order', 'snapToken'));
    }

    public function status(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        // Tarik status terkini dari Midtrans. Penting di localhost, di mana webhook
        // Midtrans tidak bisa menjangkau server sehingga status tak terupdate sendiri.
        if ($order->isPending() && $order->snap_token) {
            $this->syncStatusFromMidtrans($order);
        }

        $order->refresh();

        return view('donasi.status', compact('order'));
    }

    protected function syncStatusFromMidtrans(Order $order): void
    {
        try {
            $status = (new CheckStatusService)->getStatus($order->number);
        } catch (\Exception $e) {
            // Transaksi belum tercatat / belum dibayar → biarkan tetap pending.
            return;
        }

        $transaction = $status->transaction_status ?? null;
        $fraud       = $status->fraud_status ?? 'accept';

        if (in_array($transaction, ['capture', 'settlement'], true) && $fraud === 'accept') {
            $order->confirmPaid();
        } elseif ($transaction === 'expire') {
            $order->update(['payment_status' => Order::STATUS_EXPIRED]);
        } elseif (in_array($transaction, ['cancel', 'deny'], true)) {
            $order->update(['payment_status' => Order::STATUS_CANCELLED]);
        }
    }
}
