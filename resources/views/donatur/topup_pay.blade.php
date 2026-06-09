@extends('layouts.app')

@section('content')
<div class="w-full max-w-[640px] mx-auto px-4 py-12 flex flex-col gap-6">

    <div>
        <h1 class="text-on-surface text-3xl font-headline font-extrabold tracking-tight">Pembayaran Top Up</h1>
        <p class="text-on-surface-variant text-base font-medium mt-1">Klik tombol di bawah untuk membuka popup pembayaran QRIS.</p>
    </div>

    <div class="bg-white rounded-2xl border border-surface-container shadow-lg overflow-hidden">
        <div class="solar-gradient p-6 flex items-center justify-between">
            <div>
                <p class="text-deep-navy/70 text-xs font-bold uppercase tracking-widest">Total Top Up</p>
                <p class="text-deep-navy text-3xl font-extrabold">Rp {{ number_format($topup->amount, 0, ',', '.') }}</p>
                <p class="text-deep-navy/60 text-xs font-mono mt-1">ID: {{ $topup->number }}</p>
            </div>
            <span class="material-symbols-outlined text-deep-navy/60 text-5xl">account_balance_wallet</span>
        </div>

        <div class="p-6 flex flex-col gap-3">
            <p class="text-sm font-bold text-on-surface">Cara Pembayaran QRIS:</p>
            <div class="flex items-start gap-3 text-sm text-on-surface-variant"><span class="material-symbols-outlined text-[18px] text-primary">looks_one</span> Klik <strong>Bayar Sekarang</strong>.</div>
            <div class="flex items-start gap-3 text-sm text-on-surface-variant"><span class="material-symbols-outlined text-[18px] text-primary">looks_two</span> Pilih <strong>QRIS</strong> di popup Midtrans.</div>
            <div class="flex items-start gap-3 text-sm text-on-surface-variant"><span class="material-symbols-outlined text-[18px] text-primary">looks_3</span> Scan QR dengan dompet digital (GoPay, OVO, Dana, dll).</div>
            <div class="flex items-start gap-3 text-sm text-on-surface-variant"><span class="material-symbols-outlined text-[18px] text-primary">looks_4</span> Setelah bayar, kamu diarahkan ke halaman status.</div>
        </div>

        <div class="border-t border-surface-container bg-surface-container-low/40 p-6 text-center">
            <button id="pay-button" class="w-full bg-primary-container text-on-primary-fixed font-headline font-extrabold text-lg py-4 rounded-xl shadow-md hover:opacity-90 transition-all">
                Bayar Sekarang — Rp {{ number_format($topup->amount, 0, ',', '.') }}
            </button>
            <p class="text-xs text-on-surface-variant mt-3">🔒 Transaksi dienkripsi & diproses oleh Midtrans</p>
        </div>
    </div>
</div>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
(function () {
    const payBtn    = document.getElementById('pay-button');
    const snapToken = @json($snapToken);
    const statusUrl = @json(route('donatur.topup.status', $topup));

    if (!snapToken) {
        payBtn.disabled = true;
        payBtn.textContent = 'Token tidak tersedia';
        return;
    }

    payBtn.addEventListener('click', function () {
        payBtn.disabled = true;
        payBtn.textContent = 'Membuka Pembayaran...';
        snap.pay(snapToken, {
            onSuccess: () => window.location.href = statusUrl,
            onPending: () => window.location.href = statusUrl,
            onError:   () => { payBtn.disabled = false; payBtn.textContent = 'Bayar Sekarang'; alert('Terjadi kesalahan. Coba lagi.'); },
            onClose:   () => { payBtn.disabled = false; payBtn.textContent = 'Bayar Sekarang'; },
        });
    });
})();
</script>
@endsection
