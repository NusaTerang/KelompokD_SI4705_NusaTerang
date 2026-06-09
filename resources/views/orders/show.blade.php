@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 text-center">
                    <h2 class="text-2xl font-semibold mb-4">Selesaikan Donasi Anda</h2>
                    <p class="mb-2">Proyek: <strong>{{ $order->proyek->judul }}</strong></p>
                    <p class="mb-4">Total Donasi: <strong>Rp {{ number_format($order->total_price, 0, ',', '.') }}</strong></p>

                    @if ($order->payment_status == 1)
                        <button id="pay-button" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-150 ease-in-out">
                            Bayar dengan QRIS Sekarang
                        </button>
                    @elseif($order->payment_status == 2)
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <strong class="font-bold">Terima Kasih!</strong>
                            <span class="block sm:inline">Pembayaran donasi Anda telah berhasil.</span>
                        </div>
                    @elseif($order->payment_status == 3)
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <strong class="font-bold">Gagal!</strong>
                            <span class="block sm:inline">Waktu pembayaran telah habis (Expired).</span>
                        </div>
                    @elseif($order->payment_status == 4)
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <strong class="font-bold">Dibatalkan!</strong>
                            <span class="block sm:inline">Pembayaran dibatalkan.</span>
                        </div>
                    @endif

                    <div class="mt-6">
                        <a href="{{ route('proyek.show', $order->proyek_id) }}" class="text-blue-500 hover:underline">Kembali ke halaman Proyek</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($order->payment_status == 1)
        <!-- Tambahkan script Midtrans Snap -->
        @if(config('midtrans.is_production'))
            <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
        @else
            <script src="https://app.midtrans.com/snap/snap.js"
            data-client-key="{{ config('midtrans.client_key') }}">
</script>
        @endif
        
        <script>
            const payButton = document.querySelector('#pay-button');
            payButton.addEventListener('click', function(e) {
                e.preventDefault();
                snap.pay('{{ $snapToken }}', {
                    onSuccess: function(result) {
                        window.location.reload();
                    },
                    onPending: function(result) {
                        alert("Menunggu pembayaran Anda!");
                    },
                    onError: function(result) {
                        alert("Pembayaran gagal!");
                        window.location.reload();
                    },
                    onClose: function() {
                        alert('Anda menutup popup sebelum menyelesaikan pembayaran');
                    }
                });
            });
        </script>
    @endif
@endsection
