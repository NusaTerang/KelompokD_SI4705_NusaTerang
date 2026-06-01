<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Proyek;
use App\Services\Midtrans\CreateSnapTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Step 1 — Tampilkan form donasi.
     */
    public function create(Proyek $proyek)
    {
        // Pre-fill dari data user yang sedang login
        $user = auth()->user();

        // Pastikan proyek aktif
        if ($proyek->status !== 'aktif_funding') {
            return redirect()->route('proyek.show', $proyek->id)->with('error', 'Proyek ini sedang tidak menerima donasi.');
        }

        return view('donasi.create', compact('user', 'proyek'));
    }

    /**
     * Step 1 (POST) — Simpan donasi & generate snap token.
     */
    public function store(Request $request, Proyek $proyek)
    {
        $validated = $request->validate([
            'donatur_name'  => 'required|string|max:100',
            'donatur_email' => 'required|email|max:150',
            'donatur_phone' => 'nullable|string|max:20',
            'total_price'   => 'required|integer|min:10000|max:100000000',
            'pesan'         => 'nullable|string|max:500',
        ], [
            'donatur_name.required'  => 'Nama donatur wajib diisi.',
            'donatur_email.required' => 'Email wajib diisi.',
            'donatur_email.email'    => 'Format email tidak valid.',
            'total_price.required'   => 'Jumlah donasi wajib diisi.',
            'total_price.min'        => 'Minimum donasi Rp 10.000.',
        ]);

        // Buat order baru
        $order = Order::create([
            'user_id'        => auth()->id(),
            'proyek_id'      => $proyek->id,
            'number'         => 'NT-' . strtoupper(Str::random(10)),
            'total_price'    => $validated['total_price'],
            'donatur_name'   => $validated['donatur_name'],
            'donatur_email'  => $validated['donatur_email'],
            'donatur_phone'  => $validated['donatur_phone'] ?? null,
            'pesan'          => $validated['pesan'] ?? null,
            'payment_status' => Order::STATUS_PENDING,
        ]);

        // Generate snap token
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

    /**
     * Step 2 — Halaman pembayaran (Snap Midtrans).
     */
    public function show(Order $order)
    {
        // Pastikan hanya pemilik order yang bisa akses
        abort_unless($order->user_id === auth()->id(), 403);

        // Jika sudah tidak pending, langsung ke status
        if (!$order->isPending()) {
            return redirect()->route('donasi.status', $order);
        }

        // Force regenerate snap token
        $order->update(['snap_token' => null]);
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

    /**
     * Step 3 — Halaman status pembayaran.
     */
    public function status(Order $order)
    {
        // Pastikan hanya pemilik order yang bisa akses
        abort_unless($order->user_id === auth()->id(), 403);

        // Cek langsung ke Midtrans jika masih pending (sangat penting untuk local development
        // karena webhook Midtrans tidak bisa menjangkau localhost)
        if ($order->isPending() && $order->snap_token) {
            try {
                new \App\Services\Midtrans\Midtrans(); // Inisialisasi konfigurasi Midtrans
                $midtransStatus = \Midtrans\Transaction::status($order->number);

                if (isset($midtransStatus->transaction_status)) {
                    $status = $midtransStatus->transaction_status;
                    if (in_array($status, ['capture', 'settlement'])) {
                        $order->update(['payment_status' => Order::STATUS_SUCCESS]);
                        
                        // Tambahkan dana ke proyek terkait
                        if ($order->proyek_id) {
                            $proyekTarget = \App\Models\Proyek::find($order->proyek_id);
                            if ($proyekTarget) {
                                $proyekTarget->dana_terkumpul += $order->total_price;
                                $proyekTarget->save();
                            }
                        }
                    } elseif (in_array($status, ['expire', 'cancel', 'deny'])) {
                        $order->update(['payment_status' => Order::STATUS_EXPIRED]);
                    }
                }
            } catch (\Exception $e) {
                // Abaikan jika transaksi belum ada di Midtrans API atau koneksi gagal
            }
        }

        // Refresh dari database agar status terbaru
        $order->refresh();

        return view('donasi.status', compact('order'));
    }
}
