@php $step = 2; @endphp
@extends('layouts.donasi')

@section('title', 'Pembayaran — Rp ' . number_format($order->total_price, 0, ',', '.'))

@push('styles')
<style>
.payment-card-header {
    background: linear-gradient(135deg, #064e3b 0%, #059669 100%);
    color: #fff; padding: 2rem; border-bottom: none;
}
.payment-card-header h1 { color: #fff; font-size: 1.375rem; }
.payment-card-header p  { color: rgba(255,255,255,.75); margin-top:.375rem; }
.order-summary {
    display: flex; align-items: center; justify-content: space-between;
    margin-top: 1.25rem; background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.2); border-radius: var(--radius-sm); padding: 1rem 1.25rem;
}
.order-summary-label { font-size:.8rem; color:rgba(255,255,255,.7); margin-bottom:.25rem; }
.order-summary-value { font-size:1.5rem; font-weight:800; letter-spacing:-.5px; }
.order-id { font-size:.75rem; color:rgba(255,255,255,.6); margin-top:.5rem; font-family:monospace; }
.qris-step {
    display: flex; gap: .875rem; padding: 1rem; background: var(--clr-bg);
    border-radius: var(--radius-sm); border: 1px solid var(--clr-border); margin-bottom:.625rem;
}
.qris-num {
    width:28px; height:28px; min-width:28px; border-radius:50%;
    background:var(--clr-primary); color:#fff; font-weight:700; font-size:.8rem;
    display:flex; align-items:center; justify-content:center;
}
.qris-text { font-size:.875rem; color:var(--clr-text); line-height:1.5; }
.qris-text strong { color:var(--clr-primary-dk); }
.donatur-row {
    display:flex; padding:.625rem 1rem; font-size:.875rem;
    border-bottom:1px solid var(--clr-border);
}
.donatur-row:last-child { border-bottom:none; }
.donatur-key { width:130px; min-width:130px; color:var(--clr-muted); font-weight:500; }
.donatur-val { color:var(--clr-text); font-weight:600; word-break:break-all; }
.btn-pay {
    background: linear-gradient(135deg, #059669, #047857);
    color:#fff; border:none; padding:1rem 2.5rem;
    border-radius:var(--radius-sm); font-size:1.0625rem; font-weight:700;
    font-family:inherit; cursor:pointer; transition:var(--transition);
    display:inline-flex; align-items:center; gap:.625rem;
    box-shadow:0 4px 16px rgba(5,150,105,.35); min-width:260px; justify-content:center;
}
.btn-pay:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(5,150,105,.45); }
.btn-pay:active { transform:translateY(0); }
.btn-pay:disabled { opacity:.6; cursor:not-allowed; transform:none; }
</style>
@endpush

@section('content')
<div class="card">
    <div class="card-header payment-card-header">
        <h1>📱 Scan & Bayar dengan QRIS</h1>
        <p>Klik tombol di bawah untuk membuka popup pembayaran QRIS.</p>
        <div class="order-summary">
            <div>
                <div class="order-summary-label">Total Donasi</div>
                <div class="order-summary-value">Rp {{ number_format($order->total_price, 0, ',', '.') }}</div>
                <div class="order-id">ID: {{ $order->number }}</div>
            </div>
            <div style="font-size:2.5rem; opacity:.8;">💚</div>
        </div>
    </div>

    <div class="card-body">
        {{-- Donatur Info --}}
        <div style="border:1px solid var(--clr-border); border-radius:var(--radius-sm); overflow:hidden; margin-bottom:1.5rem;">
            <div class="donatur-row"><span class="donatur-key">Nama</span><span class="donatur-val">{{ $order->donatur_name }}</span></div>
            <div class="donatur-row"><span class="donatur-key">Email</span><span class="donatur-val">{{ $order->donatur_email }}</span></div>
            @if($order->pesan)
            <div class="donatur-row"><span class="donatur-key">Pesan</span><span class="donatur-val" style="font-style:italic;">"{{ $order->pesan }}"</span></div>
            @endif
        </div>

        {{-- Steps --}}
        <p style="font-weight:700; font-size:.9rem; margin-bottom:.875rem;">Cara Pembayaran QRIS:</p>
        <div class="qris-step"><span class="qris-num">1</span><span class="qris-text">Klik tombol <strong>"Bayar Sekarang"</strong> di bawah.</span></div>
        <div class="qris-step"><span class="qris-num">2</span><span class="qris-text">Pilih metode <strong>QRIS</strong> di halaman Midtrans yang muncul.</span></div>
        <div class="qris-step"><span class="qris-num">3</span><span class="qris-text"><strong>Scan QR code</strong> dengan dompet digital Anda (GoPay, OVO, Dana, dll).</span></div>
        <div class="qris-step"><span class="qris-num">4</span><span class="qris-text">Setelah berhasil, Anda akan <strong>diarahkan otomatis</strong> ke halaman konfirmasi.</span></div>
    </div>

    <div style="border-top:1px solid var(--clr-border); background:#fafafa; padding:1.5rem 2rem; text-align:center;">
        <button id="pay-button" class="btn-pay">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
            Bayar Sekarang — Rp {{ number_format($order->total_price, 0, ',', '.') }}
        </button>
        <div style="display:flex; align-items:center; justify-content:center; gap:.375rem; font-size:.78rem; color:var(--clr-muted); margin-top:.875rem;">
            🔒 Transaksi dienkripsi & diproses oleh Midtrans
        </div>
        <div style="margin-top:1rem;">
            <a href="{{ route('donasi.create') }}" style="font-size:.8rem; color:var(--clr-muted); text-decoration:none;">← Batalkan & buat donasi baru</a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
(function() {
    const payBtn    = document.getElementById('pay-button');
    const snapToken = @json($snapToken);
    const statusUrl = @json(route('donasi.status', $order));
    const origLabel = payBtn.innerHTML;
    const loadLabel = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" opacity=".25"/><path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"><animateTransform attributeName="transform" type="rotate" from="0 12 12" to="360 12 12" dur=".8s" repeatCount="indefinite"/></path></svg> Membuka Pembayaran...';

    if (!snapToken) {
        payBtn.disabled = true;
        payBtn.textContent = 'Token tidak tersedia';
        return;
    }

    payBtn.addEventListener('click', function() {
        payBtn.disabled = true;
        payBtn.innerHTML = loadLabel;

        snap.pay(snapToken, {
            onSuccess: function(result) { window.location.href = statusUrl; },
            onPending: function(result) { window.location.href = statusUrl; },
            onError:   function(result) {
                payBtn.disabled = false;
                payBtn.innerHTML = origLabel;
                alert('Terjadi kesalahan. Silakan coba lagi.');
            },
            onClose: function() {
                payBtn.disabled = false;
                payBtn.innerHTML = origLabel;
            }
        });
    });
})();
</script>
@endpush
