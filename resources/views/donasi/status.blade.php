@php $step = 3; @endphp
@extends('layouts.donasi')

@section('title',
    $order->isSuccess()   ? 'Donasi Berhasil 🎉' :
    ($order->isCancelled() ? 'Donasi Dibatalkan' :
    ($order->isExpired()   ? 'Donasi Kadaluarsa' : 'Menunggu Pembayaran'))
)

@push('styles')
<style>
.status-icon-wrap {
    width: 88px; height: 88px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 2.5rem; margin: 0 auto 1.25rem;
}
.status-success  .status-icon-wrap { background: var(--clr-success-lt); }
.status-pending  .status-icon-wrap { background: var(--clr-warning-lt); }
.status-failed   .status-icon-wrap { background: var(--clr-danger-lt);  }

.status-title {
    font-size: 1.5rem; font-weight: 800; text-align: center; margin-bottom: .5rem;
}
.status-success  .status-title { color: var(--clr-success); }
.status-pending  .status-title { color: var(--clr-warning); }
.status-failed   .status-title { color: var(--clr-danger);  }

.status-subtitle {
    text-align: center; color: var(--clr-muted); font-size: .9rem;
    line-height: 1.6; margin-bottom: 1.75rem;
}

.receipt-box {
    background: #fafafa; border: 1px solid var(--clr-border);
    border-radius: var(--radius-sm); overflow: hidden; margin-bottom: 1.5rem;
}
.receipt-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: .75rem 1.25rem; border-bottom: 1px solid var(--clr-border);
    font-size: .875rem;
}
.receipt-row:last-child { border-bottom: none; }
.receipt-key { color: var(--clr-muted); font-weight: 500; }
.receipt-val { font-weight: 700; color: var(--clr-text); text-align: right; }
.receipt-val.amount { font-size: 1.1rem; color: var(--clr-primary-dk); }

.badge {
    display: inline-flex; align-items: center; gap: .375rem;
    padding: .25rem .75rem; border-radius: 100px;
    font-size: .78rem; font-weight: 700;
}
.badge-success  { background: var(--clr-success-lt); color: var(--clr-success); }
.badge-pending  { background: var(--clr-warning-lt); color: #b45309; }
.badge-danger   { background: var(--clr-danger-lt);  color: var(--clr-danger);  }

.pesan-donatur {
    background: var(--clr-primary-lt); border: 1px solid #a7f3d0;
    border-radius: var(--radius-sm); padding: 1rem 1.25rem;
    margin-bottom: 1.5rem; font-style: italic; color: var(--clr-primary-dk);
    font-size: .9rem; line-height: 1.6;
}

.actions { display: flex; flex-direction: column; gap: .75rem; }
.btn-green {
    display: flex; align-items: center; justify-content: center; gap: .5rem;
    padding: .875rem 1.5rem; background: var(--clr-primary); color: #fff;
    border-radius: var(--radius-sm); font-weight: 700; font-size: .9375rem;
    text-decoration: none; transition: var(--transition); border: none;
    font-family: inherit; cursor: pointer;
}
.btn-green:hover { background: var(--clr-primary-dk); transform: translateY(-1px); }
.btn-ghost {
    display: flex; align-items: center; justify-content: center; gap: .5rem;
    padding: .875rem 1.5rem; background: transparent; color: var(--clr-muted);
    border: 1.5px solid var(--clr-border); border-radius: var(--radius-sm);
    font-weight: 600; font-size: .9375rem; text-decoration: none;
    transition: var(--transition);
}
.btn-ghost:hover { border-color: var(--clr-primary); color: var(--clr-primary); }

/* Pulse animation for pending icon */
@keyframes pulse-ring {
    0%   { box-shadow: 0 0 0 0 rgba(245,158,11,.4); }
    70%  { box-shadow: 0 0 0 16px rgba(245,158,11,0); }
    100% { box-shadow: 0 0 0 0 rgba(245,158,11,0); }
}
.status-pending .status-icon-wrap { animation: pulse-ring 1.8s ease-out infinite; }

/* Bounce animation for success */
@keyframes bounce-in {
    0%   { transform: scale(.5); opacity:0; }
    60%  { transform: scale(1.1); }
    100% { transform: scale(1); opacity:1; }
}
.status-success .status-icon-wrap { animation: bounce-in .5s cubic-bezier(.36,.07,.19,.97); }
</style>
@endpush

@section('content')

@php
    $isSuccess   = $order->isSuccess();
    $isPending   = $order->isPending();
    $isCancelled = $order->isCancelled();
    $isExpired   = $order->isExpired();
    $statusClass = $isSuccess ? 'status-success' : ($isPending ? 'status-pending' : 'status-failed');
@endphp

<div class="card {{ $statusClass }}">
    <div class="card-body" style="text-align:center; padding-top:2.5rem;">

        {{-- Status Icon --}}
        <div class="status-icon-wrap">
            @if($isSuccess)   🎉
            @elseif($isPending) ⏳
            @else              ❌
            @endif
        </div>

        {{-- Title --}}
        <h1 class="status-title">
            @if($isSuccess)    Donasi Berhasil!
            @elseif($isPending) Menunggu Pembayaran
            @elseif($isCancelled) Donasi Dibatalkan
            @else               Donasi Kadaluarsa
            @endif
        </h1>

        {{-- Subtitle --}}
        <p class="status-subtitle">
            @if($isSuccess)
                Terima kasih, <strong>{{ $order->donatur_name }}</strong>! 💚<br>
                Donasi Anda akan membantu menerangi desa-desa yang membutuhkan.
            @elseif($isPending)
                Pembayaran Anda <strong>belum terkonfirmasi</strong>.<br>
                Jika sudah membayar, tunggu beberapa saat dan refresh halaman ini.
            @elseif($isCancelled)
                Donasi Anda telah dibatalkan. Anda dapat membuat donasi baru kapan saja.
            @else
                Waktu pembayaran telah habis. Silakan buat donasi baru.
            @endif
        </p>
    </div>

    {{-- Receipt --}}
    <div class="card-body" style="border-top:1px solid var(--clr-border); padding-top:1.5rem;">
        @if($order->pesan)
        <div class="pesan-donatur">
            💬 "{{ $order->pesan }}"
        </div>
        @endif

        <div class="receipt-box">
            <div class="receipt-row">
                <span class="receipt-key">ID Transaksi</span>
                <span class="receipt-val" style="font-family:monospace; font-size:.8rem;">{{ $order->number }}</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-key">Donatur</span>
                <span class="receipt-val">{{ $order->donatur_name }}</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-key">Email</span>
                <span class="receipt-val">{{ $order->donatur_email }}</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-key">Jumlah Donasi</span>
                <span class="receipt-val amount">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-key">Metode</span>
                <span class="receipt-val">QRIS</span>
            </div>
            <div class="receipt-row">
                <span class="receipt-key">Status</span>
                <span class="receipt-val">
                    @if($isSuccess)
                        <span class="badge badge-success">✓ Berhasil</span>
                    @elseif($isPending)
                        <span class="badge badge-pending">⏳ Menunggu</span>
                    @elseif($isCancelled)
                        <span class="badge badge-danger">✕ Dibatalkan</span>
                    @else
                        <span class="badge badge-danger">✕ Kadaluarsa</span>
                    @endif
                </span>
            </div>
            <div class="receipt-row">
                <span class="receipt-key">Tanggal</span>
                <span class="receipt-val">{{ $order->created_at->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }} WIB</span>
            </div>
        </div>

        {{-- Action buttons --}}
        <div class="actions">
            @if($isSuccess)
                <a href="{{ route('donasi.create') }}" class="btn-green">
                    💚 Donasi Lagi
                </a>
                <a href="{{ url('/') }}" class="btn-ghost">← Kembali ke Beranda</a>
            @elseif($isPending)
                <button onclick="window.location.reload()" class="btn-green">
                    🔄 Refresh Status
                </button>
                <a href="{{ route('donasi.show', $order) }}" class="btn-ghost">
                    ← Kembali ke Halaman Bayar
                </a>
            @else
                <a href="{{ route('donasi.create') }}" class="btn-green">
                    ✚ Buat Donasi Baru
                </a>
                <a href="{{ url('/') }}" class="btn-ghost">← Kembali ke Beranda</a>
            @endif
        </div>
    </div>
</div>

{{-- Auto-refresh for pending status --}}
@if($isPending)
<p style="text-align:center; font-size:.78rem; color:var(--clr-muted); margin-top:1rem;">
    Halaman ini akan otomatis diperbarui dalam <span id="countdown">30</span> detik...
</p>
@push('scripts')
<script>
let seconds = 30;
const el = document.getElementById('countdown');
const timer = setInterval(() => {
    seconds--;
    el.textContent = seconds;
    if (seconds <= 0) { clearInterval(timer); window.location.reload(); }
}, 1000);
</script>
@endpush
@endif

@endsection
