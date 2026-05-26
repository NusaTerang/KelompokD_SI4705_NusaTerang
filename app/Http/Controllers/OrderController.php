<?php

namespace App\Http\Controllers;

use App\Models\Order;
<<<<<<< Updated upstream
use App\Services\Midtrans\CreateSnapTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Step 1 — Tampilkan form donasi.
     */
    public function create()
    {
        // Pre-fill dari data user yang sedang login
        $user = auth()->user();

        return view('donasi.create', compact('user'));
    }

    /**
     * Step 1 (POST) — Simpan donasi & generate snap token.
     */
    public function store(Request $request)
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

        // Re-generate snap token jika null
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

        // Refresh dari database agar status terbaru
        $order->refresh();

        return view('donasi.status', compact('order'));
=======
use App\Models\Proyek;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\Midtrans\CreateSnapTokenService;

class OrderController extends Controller
{
    public function store(Request $request, $proyek_id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
        ]);

        $proyek = Proyek::findOrFail($proyek_id);
        
        $order = Order::create([
            'number' => 'DONASI-' . strtoupper(Str::random(10)),
            'total_price' => $request->amount,
            'payment_status' => 1, // pending
            'proyek_id' => $proyek->id,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('order.show', $order->id);
    }

    public function show(Order $order)
    {
        // Pastikan hanya donatur pemilik order yang bisa melihat
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $snapToken = $order->snap_token;
        if (is_null($snapToken)) {
            // Generate snap token and save it to database
            $midtrans = new CreateSnapTokenService($order);
            $snapToken = $midtrans->getSnapToken();

            $order->snap_token = $snapToken;
            $order->save();
        }

        return view('orders.show', compact('order', 'snapToken'));
>>>>>>> Stashed changes
    }
}