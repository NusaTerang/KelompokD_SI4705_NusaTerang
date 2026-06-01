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
        $user = auth()->user();
        return view('donasi.create', compact('user'));
    }

    public function store(Request $request, Proyek $proyek)
    {
        $validated = $request->validate([
            'amount' => 'required|integer|min:10000|max:100000000',
        ], [
            'amount.required' => 'Jumlah donasi wajib diisi.',
            'amount.min'      => 'Minimum donasi Rp 10.000.',
        ]);

        $user = auth()->user();

        $order = Order::create([
            'user_id'        => auth()->id(),
            'proyek_id'      => $proyek->id,
            'number'         => 'NT-' . strtoupper(Str::random(10)),
            'total_price'    => $validated['amount'],
            'donatur_name'   => $user->name,
            'donatur_email'  => $user->email,
            'donatur_phone'  => $user->no_telepon ?? null,
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
