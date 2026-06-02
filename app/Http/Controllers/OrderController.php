<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Proyek;
use App\Services\Midtrans\CreateSnapTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function create()
    {
        $user   = auth()->user();
        $proyek = request()->filled('proyek') ? Proyek::find(request('proyek')) : null;
        return view('donasi.create', compact('user', 'proyek'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'proyek_id'     => 'required|exists:proyeks,id',
            'donatur_name'  => 'required|string|max:100',
            'donatur_email' => 'required|email|max:150',
            'donatur_phone' => 'nullable|string|max:20',
            'total_price'   => 'required|integer|min:10000|max:100000000',
            'pesan'         => 'nullable|string|max:500',
        ], [
            'proyek_id.required'     => 'Proyek tidak valid.',
            'donatur_name.required'  => 'Nama donatur wajib diisi.',
            'donatur_email.required' => 'Email wajib diisi.',
            'total_price.required'   => 'Jumlah donasi wajib diisi.',
            'total_price.min'        => 'Minimum donasi Rp 10.000.',
        ]);

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

        $order->refresh();

        return view('donasi.status', compact('order'));
    }
}
