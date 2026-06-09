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
        $user   = auth()->user()->load('saldoDonatur');
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

        return redirect()->route('donasi.show', $order);
    }

    public function show(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        if (!$order->isPending()) {
            return redirect()->route('donasi.status', $order);
        }

        $user = auth()->user();
        $saldo = $user->saldo;

        // Auto-select QRIS if balance is 0 and method not yet selected
        if (is_null($order->payment_method) && $saldo <= 0) {
            $order->update([
                'payment_method' => 'qris',
                'amount_saldo' => 0,
                'amount_qris' => $order->total_price,
            ]);
        }

        // If payment method is already selected (e.g. qris or kombinasi), generate snap token if not present
        if (!is_null($order->payment_method) && $order->payment_method !== 'saldo') {
            if (is_null($order->snap_token)) {
                try {
                    $midtrans  = new CreateSnapTokenService($order);
                    $snapToken = $midtrans->getSnapToken();
                    $order->update(['snap_token' => $snapToken]);
                } catch (\Exception $e) {
                    return back()->withErrors(['payment' => 'Gagal generate token pembayaran: ' . $e->getMessage()]);
                }
            }
        }

        $snapToken = $order->snap_token;

        return view('donasi.show', compact('order', 'snapToken', 'saldo'));
    }

    public function selectMethod(Order $order, Request $request)
    {
        abort_unless($order->user_id === auth()->id(), 403);
        abort_unless($order->isPending(), 400);

        $validated = $request->validate([
            'payment_method' => 'required|in:saldo,qris,kombinasi',
        ]);

        $method = $validated['payment_method'];
        $user = auth()->user();
        $saldo = $user->saldo;

        if ($method === 'saldo') {
            if ($saldo < $order->total_price) {
                return back()->withErrors(['payment' => 'Saldo tidak mencukupi untuk pembayaran saldo penuh.']);
            }
            
            $order->update([
                'payment_method' => 'saldo',
                'amount_saldo' => $order->total_price,
                'amount_qris' => 0,
                'snap_token' => null,
            ]);
        } elseif ($method === 'qris') {
            $order->update([
                'payment_method' => 'qris',
                'amount_saldo' => 0,
                'amount_qris' => $order->total_price,
                'snap_token' => null,
            ]);
        } elseif ($method === 'kombinasi') {
            if ($saldo <= 0) {
                return back()->withErrors(['payment' => 'Saldo Anda 0, tidak dapat memilih opsi kombinasi.']);
            }
            if ($saldo >= $order->total_price) {
                return back()->withErrors(['payment' => 'Saldo Anda mencukupi, silakan gunakan opsi Bayar dengan Saldo.']);
            }

            $order->update([
                'payment_method' => 'kombinasi',
                'amount_saldo' => $saldo,
                'amount_qris' => $order->total_price - $saldo,
                'snap_token' => null,
            ]);
        }

        return redirect()->route('donasi.show', $order);
    }

    public function confirmSaldo(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);
        abort_unless($order->isPending(), 400);
        abort_unless($order->payment_method === 'saldo', 400);

        $user = auth()->user();
        if ($user->saldo < $order->total_price) {
            return back()->withErrors(['payment' => 'Saldo tidak mencukupi.']);
        }

        $proyek = $order->proyek;
        if (!$proyek) {
            return back()->withErrors(['payment' => 'Proyek tidak ditemukan.']);
        }

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($order, $user, $proyek) {
                // Deduct balance
                $user->kurangiSaldo($order->total_price, "Donasi saldo untuk proyek: {$proyek->judul}");

                // Update order status
                $order->update([
                    'payment_status' => Order::STATUS_SUCCESS,
                ]);

                // Create success Donasi record
                \App\Models\Donasi::updateOrCreate(
                    [
                        'id_proyek' => $proyek->id,
                        'id_donatur' => $order->user_id,
                        'created_at' => $order->created_at ?? now(),
                    ],
                    [
                        'nominal' => $order->total_price,
                        'status' => 'success',
                    ]
                );

                // Increment the project's collected funding
                $proyek->increment('dana_terkumpul', $order->total_price);
                $proyek->refresh();

                // Transition status: only change to eksekusi when target reached
                if ($proyek->dana_terkumpul >= $proyek->target_dana) {
                    $proyek->update(['status' => 'eksekusi']);
                }
                // Otherwise, tetap aktif_funding (no need to update)
            });
        } catch (\Exception $e) {
            return back()->withErrors(['payment' => 'Terjadi kesalahan saat memproses pembayaran saldo: ' . $e->getMessage()]);
        }

        return redirect()->route('donasi.status', $order);
    }

    public function resetMethod(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);
        abort_unless($order->isPending(), 400);

        $order->update([
            'payment_method' => null,
            'snap_token' => null,
            'amount_saldo' => 0,
            'amount_qris' => 0,
        ]);

        return redirect()->route('donasi.show', $order);
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
