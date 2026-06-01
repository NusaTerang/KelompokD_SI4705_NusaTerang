<?php

namespace App\Http\Controllers;

use App\Models\Order;
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
            'payment_status' => Order::STATUS_PENDING,
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
    }
}