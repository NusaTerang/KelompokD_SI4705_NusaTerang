@extends('layouts.app')

@section('content')
@php
    $isSuccess   = $topup->isSuccess();
    $isPending   = $topup->isPending();
    $isCancelled = $topup->isCancelled();
    $isExpired   = $topup->isExpired();
@endphp
<div class="w-full max-w-[560px] mx-auto px-4 py-12">
    <div class="bg-white rounded-2xl border border-surface-container shadow-lg p-8 flex flex-col items-center text-center gap-3">

        <div class="w-20 h-20 rounded-full flex items-center justify-center text-4xl
            {{ $isSuccess ? 'bg-green-100' : ($isPending ? 'bg-amber-100' : 'bg-red-100') }}">
            @if($isSuccess) 🎉 @elseif($isPending) ⏳ @else ❌ @endif
        </div>

        <h1 class="text-2xl font-headline font-extrabold {{ $isSuccess ? 'text-green-700' : ($isPending ? 'text-amber-600' : 'text-red-600') }}">
            @if($isSuccess) Top Up Berhasil!
            @elseif($isPending) Menunggu Pembayaran
            @elseif($isCancelled) Top Up Dibatalkan
            @else Top Up Kadaluarsa @endif
        </h1>

        <p class="text-on-surface-variant text-sm leading-relaxed">
            @if($isSuccess)
                Saldo kamu telah bertambah <strong>Rp {{ number_format($topup->amount, 0, ',', '.') }}</strong>.
            @elseif($isPending)
                Pembayaran <strong>belum terkonfirmasi</strong>. Jika sudah membayar, tunggu sebentar lalu klik Refresh Status.
            @elseif($isCancelled)
                Top up dibatalkan. Kamu bisa membuat top up baru kapan saja.
            @else
                Waktu pembayaran habis. Silakan buat top up baru.
            @endif
        </p>

        <div class="w-full bg-surface-container-low rounded-xl divide-y divide-surface-container mt-2 text-sm">
            <div class="flex justify-between px-4 py-3"><span class="text-on-surface-variant">ID Transaksi</span><span class="font-mono font-bold">{{ $topup->number }}</span></div>
            <div class="flex justify-between px-4 py-3"><span class="text-on-surface-variant">Nominal</span><span class="font-bold text-deep-navy">Rp {{ number_format($topup->amount, 0, ',', '.') }}</span></div>
            <div class="flex justify-between px-4 py-3"><span class="text-on-surface-variant">Metode</span><span class="font-bold">QRIS</span></div>
            <div class="flex justify-between px-4 py-3">
                <span class="text-on-surface-variant">Status</span>
                <span class="font-bold {{ $isSuccess ? 'text-green-700' : ($isPending ? 'text-amber-600' : 'text-red-600') }}">
                    @if($isSuccess) ✓ Berhasil @elseif($isPending) ⏳ Menunggu @elseif($isCancelled) ✕ Dibatalkan @else ✕ Kadaluarsa @endif
                </span>
            </div>
            <div class="flex justify-between px-4 py-3"><span class="text-on-surface-variant">Tanggal</span><span class="font-bold">{{ $topup->created_at->translatedFormat('d M Y, H:i') }} WIB</span></div>
        </div>

        <div class="w-full flex flex-col gap-3 mt-4">
            @if($isSuccess)
                <a href="{{ route('donatur.saldo') }}" class="w-full bg-primary-container text-on-primary-fixed font-bold py-3.5 rounded-xl hover:opacity-90 transition-all">Lihat Saldo</a>
                <a href="{{ url('/proyek') }}" class="w-full border border-outline-variant text-on-surface font-bold py-3.5 rounded-xl hover:bg-surface-container-low transition-colors">Donasi Sekarang</a>
            @elseif($isPending)
                <button onclick="window.location.reload()" class="w-full bg-primary-container text-on-primary-fixed font-bold py-3.5 rounded-xl hover:opacity-90 transition-all">🔄 Refresh Status</button>
                <a href="{{ route('donatur.topup.show', $topup) }}" class="w-full border border-outline-variant text-on-surface font-bold py-3.5 rounded-xl hover:bg-surface-container-low transition-colors">← Kembali ke Halaman Bayar</a>
            @else
                <a href="{{ route('donatur.topup.create') }}" class="w-full bg-primary-container text-on-primary-fixed font-bold py-3.5 rounded-xl hover:opacity-90 transition-all">Buat Top Up Baru</a>
                <a href="{{ route('donatur.saldo') }}" class="w-full border border-outline-variant text-on-surface font-bold py-3.5 rounded-xl hover:bg-surface-container-low transition-colors">← Kembali ke Saldo</a>
            @endif
        </div>
    </div>

    @if($isPending)
        <p class="text-center text-xs text-on-surface-variant mt-4">Halaman ini akan otomatis diperbarui dalam <span id="countdown">30</span> detik...</p>
        <script>
            let s = 30; const el = document.getElementById('countdown');
            const t = setInterval(() => { s--; el.textContent = s; if (s <= 0) { clearInterval(t); window.location.reload(); } }, 1000);
        </script>
    @endif
</div>
@endsection
