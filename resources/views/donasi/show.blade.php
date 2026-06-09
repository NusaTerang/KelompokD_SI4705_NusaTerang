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

/* Selection view styles */
.payment-methods-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.25rem;
    margin-bottom: 1.5rem;
}
@media (min-width: 640px) {
    .payment-methods-grid {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    }
}
.method-card {
    background: #fff;
    border: 2px solid var(--clr-border);
    border-radius: var(--radius);
    padding: 1.5rem;
    cursor: pointer;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}
.method-card:hover:not(.disabled) {
    transform: translateY(-4px) scale(1.01);
    box-shadow: var(--shadow-lg);
    border-color: var(--clr-primary);
}
.method-card.selected {
    border-color: var(--clr-primary);
    background: var(--clr-success-lt);
    box-shadow: 0 4px 20px rgba(5, 150, 105, 0.15);
}
.method-card.disabled {
    opacity: 0.6;
    cursor: not-allowed;
    background: #f8fafc;
}
.method-icon-wrap {
    font-size: 2.25rem;
    margin-bottom: 0.75rem;
}
.method-title {
    font-size: 1.15rem;
    font-weight: 800;
    color: var(--clr-text);
    margin-bottom: 0.375rem;
}
.method-desc {
    font-size: 0.85rem;
    color: var(--clr-muted);
    line-height: 1.5;
    margin-bottom: 1rem;
    flex-grow: 1;
}
.saldo-summary {
    background: #f8fafc;
    border-radius: var(--radius-sm);
    padding: 0.75rem 1rem;
    font-size: 0.82rem;
    color: var(--clr-text);
}
.method-card.selected .saldo-summary {
    background: #fff;
    border: 1px solid var(--clr-primary-lt);
}
.summary-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.375rem;
}
.summary-row:last-child {
    margin-bottom: 0;
}
.summary-row.success {
    color: var(--clr-success);
    font-weight: 700;
}
.summary-row.danger {
    color: var(--clr-danger);
    font-weight: 700;
}
.summary-row.warning {
    color: var(--clr-warning);
    font-weight: 700;
}
.badge-disabled-reason {
    position: absolute;
    top: 0.75rem;
    right: 0.75rem;
    background: var(--clr-danger-lt);
    color: var(--clr-danger);
    font-size: 0.7rem;
    font-weight: 700;
    padding: 0.2rem 0.5rem;
    border-radius: 100px;
    border: 1px solid var(--clr-danger);
}
</style>
@endpush

@section('content')
@if(is_null($order->payment_method))
    {{-- STEP 2: METHOD SELECTION --}}
    <div class="card">
        <div class="card-header" style="background: linear-gradient(135deg, #064e3b 0%, #059669 100%); color: #fff; padding: 2rem;">
            <h1 style="color:#fff;">💳 Pilih Metode Pembayaran</h1>
            <p style="color:rgba(255,255,255,0.8);">Silakan pilih salah satu metode pembayaran di bawah untuk melanjutkan donasi.</p>
        </div>

        <div class="card-body">
            <h3 style="font-weight: 700; font-size: 1.1rem; margin-bottom: 1.25rem; color: var(--clr-text);">Metode Pembayaran:</h3>
            <div class="payment-methods-grid">
                <!-- Option 1: Bayar dengan Saldo -->
                @php
                    $isSaldoCukup = $saldo >= $order->total_price;
                @endphp
                <div class="method-card {{ $isSaldoCukup ? '' : 'disabled' }}" data-method="saldo" onclick="{{ $isSaldoCukup ? "selectMethod('saldo')" : '' }}">
                    @if(!$isSaldoCukup)
                        <span class="badge-disabled-reason">Saldo Tidak Mencukupi</span>
                    @endif
                    <div class="method-icon-wrap">💰</div>
                    <div class="method-title">Bayar dengan Saldo</div>
                    <div class="method-desc">Donasi praktis menggunakan saldo NusaTerang Anda yang aktif.</div>
                    <div class="saldo-summary">
                        <div class="summary-row"><span>Saldo Anda:</span><strong>Rp {{ number_format($saldo, 0, ',', '.') }}</strong></div>
                        <div class="summary-row"><span>Total Donasi:</span><strong>Rp {{ number_format($order->total_price, 0, ',', '.') }}</strong></div>
                        @if($isSaldoCukup)
                        <hr style="margin: 0.5rem 0; border: none; border-top: 1px solid var(--clr-border);">
                        <div class="summary-row success"><span>Sisa Saldo:</span><strong>Rp {{ number_format($saldo - $order->total_price, 0, ',', '.') }}</strong></div>
                        @endif
                    </div>
                </div>

                <!-- Option 2: Bayar dengan QRIS -->
                <div class="method-card" data-method="qris" onclick="selectMethod('qris')">
                    <div class="method-icon-wrap">📱</div>
                    <div class="method-title">Bayar dengan QRIS</div>
                    <div class="method-desc">Scan QR Code dengan GoPay, OVO, Dana, ShopeePay, BCA, dll.</div>
                    <div class="saldo-summary">
                        <div class="summary-row"><span>Total Bayar:</span><strong>Rp {{ number_format($order->total_price, 0, ',', '.') }}</strong></div>
                    </div>
                </div>

                <!-- Option 3: Gunakan Saldo + QRIS -->
                @if($saldo > 0 && $saldo < $order->total_price)
                <div class="method-card" data-method="kombinasi" onclick="selectMethod('kombinasi')">
                    <div class="method-icon-wrap">⚡</div>
                    <div class="method-title">Gunakan Saldo + QRIS</div>
                    <div class="method-desc">Gunakan semua saldo Anda, bayar sisanya dengan QRIS.</div>
                    <div class="saldo-summary">
                        <div class="summary-row"><span>Saldo Terpakai:</span><strong>Rp {{ number_format($saldo, 0, ',', '.') }}</strong></div>
                        <div class="summary-row warning"><span>Kekurangan via QRIS:</span><strong>Rp {{ number_format($order->total_price - $saldo, 0, ',', '.') }}</strong></div>
                    </div>
                </div>
                @endif
            </div>

            <form id="method-form" method="POST" action="{{ route('donasi.select-method', $order) }}">
                @csrf
                <input type="hidden" name="payment_method" id="payment-method-input">
                <button type="submit" id="btn-submit-method" class="btn btn-primary btn-lg w-full" disabled style="margin-top:1.5rem">
                    Lanjutkan Pembayaran
                </button>
            </form>
        </div>
    </div>

@elseif($order->payment_method === 'saldo')
    {{-- RINGKASAN KONFIRMASI SALDO --}}
    <div class="card">
        <div class="card-header" style="background: linear-gradient(135deg, #064e3b 0%, #059669 100%); color: #fff; padding: 2rem;">
            <h1 style="color:#fff;">💚 Konfirmasi Donasi Saldo</h1>
            <p style="color:rgba(255,255,255,0.8);">Tinjau detail donasi Anda sebelum melakukan konfirmasi pembayaran.</p>
        </div>

        <div class="card-body">
            <div style="border:1px solid var(--clr-border); border-radius:var(--radius-sm); overflow:hidden; margin-bottom:1.5rem;">
                <div class="donatur-row"><span class="donatur-key">Proyek</span><span class="donatur-val" style="color:var(--clr-primary-dk)">{{ $order->proyek->judul }}</span></div>
                <div class="donatur-row"><span class="donatur-key">Nama Donatur</span><span class="donatur-val">{{ $order->donatur_name }}</span></div>
                <div class="donatur-row"><span class="donatur-key">Email</span><span class="donatur-val">{{ $order->donatur_email }}</span></div>
                @if($order->pesan)
                <div class="donatur-row"><span class="donatur-key">Pesan</span><span class="donatur-val" style="font-style:italic;">"{{ $order->pesan }}"</span></div>
                @endif
            </div>

            <div style="background: #f8fafc; border: 1px solid var(--clr-border); border-radius: var(--radius); padding: 1.5rem; margin-bottom: 2rem;">
                <h4 style="font-weight:700; margin-bottom:1rem; font-size:1rem; border-bottom:1.5px solid var(--clr-border); padding-bottom:.5rem;">Ringkasan Pembayaran</h4>
                <div class="summary-row" style="margin-bottom: .75rem; font-size:.9rem;">
                    <span>Saldo NusaTerang:</span>
                    <strong>Rp {{ number_format($saldo, 0, ',', '.') }}</strong>
                </div>
                <div class="summary-row" style="margin-bottom: .75rem; font-size:.9rem; color:var(--clr-danger)">
                    <span>Nominal Donasi:</span>
                    <strong>- Rp {{ number_format($order->total_price, 0, ',', '.') }}</strong>
                </div>
                <hr style="margin: 0.75rem 0; border: none; border-top: 1.5px solid var(--clr-border);">
                <div class="summary-row success" style="font-size:1.1rem;">
                    <span>Saldo Akhir:</span>
                    <strong>Rp {{ number_format($saldo - $order->total_price, 0, ',', '.') }}</strong>
                </div>
            </div>

            <form method="POST" action="{{ route('donasi.confirm-saldo', $order) }}" id="form-confirm-saldo">
                @csrf
                <button type="submit" class="btn-pay w-full btn-lg" style="width:100%">
                    Konfirmasi Donasi
                </button>
            </form>

            <form method="POST" action="{{ route('donasi.reset-method', $order) }}" style="text-align:center; margin-top:1.25rem;">
                @csrf
                <button type="submit" style="background:none; border:none; color:var(--clr-muted); font-size:.85rem; cursor:pointer; text-decoration:underline;">
                    ← Pilih metode pembayaran lain
                </button>
            </form>
        </div>
    </div>

@else
    {{-- QRIS OR COMBINATION --}}
    <div class="card">
        <div class="card-header payment-card-header">
            @if($order->payment_method === 'kombinasi')
                <h1>⚡ Pembayaran Kombinasi (Saldo + QRIS)</h1>
                <p>Gunakan saldo Anda dan scan sisanya dengan QRIS.</p>
            @else
                <h1>📱 Scan & Bayar dengan QRIS</h1>
                <p>Klik tombol di bawah untuk membuka popup pembayaran QRIS.</p>
            @endif
            <div class="order-summary">
                <div>
                    @if($order->payment_method === 'kombinasi')
                        <div class="order-summary-label" style="opacity: 0.8">Total Donasi: Rp {{ number_format($order->total_price, 0, ',', '.') }}</div>
                        <div class="order-summary-label">Bayar Kekurangan (QRIS):</div>
                        <div class="order-summary-value">Rp {{ number_format($order->amount_qris, 0, ',', '.') }}</div>
                    @else
                        <div class="order-summary-label">Total Donasi:</div>
                        <div class="order-summary-value">Rp {{ number_format($order->total_price, 0, ',', '.') }}</div>
                    @endif
                    <div class="order-id">ID: {{ $order->number }}</div>
                </div>
                <div style="font-size:2.5rem; opacity:.8;">💚</div>
            </div>
        </div>

        <div class="card-body">
            @if($order->payment_method === 'kombinasi')
            <div style="background: var(--clr-warning-lt); border: 1.5px dashed var(--clr-accent); border-radius: var(--radius-sm); padding: 1rem; margin-bottom: 1.5rem;">
                <p style="font-size: .88rem; font-weight: 700; color: #b45309; margin-bottom: .25rem;">Rincian Pembayaran Kombinasi:</p>
                <div class="summary-row" style="font-size: .82rem; margin-bottom: .25rem;"><span>Saldo digunakan:</span><strong>Rp {{ number_format($order->amount_saldo, 0, ',', '.') }}</strong></div>
                <div class="summary-row" style="font-size: .82rem;"><span>Kekurangan via QRIS:</span><strong style="color:var(--clr-primary-dk)">Rp {{ number_format($order->amount_qris, 0, ',', '.') }}</strong></div>
            </div>
            @endif

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
            <div class="qris-step"><span class="qris-num">2</span><span class="qris-text">Scan QR code menggunakan e-wallet (GoPay, OVO, Dana, dll).</span></div>
            @if($order->payment_method === 'kombinasi')
                <div class="qris-step"><span class="qris-num">3</span><span class="qris-text">Setelah QRIS sukses, <strong>saldo Anda Rp {{ number_format($order->amount_saldo, 0, ',', '.') }} otomatis dideduct</strong>.</span></div>
            @else
                <div class="qris-step"><span class="qris-num">3</span><span class="qris-text">Setelah pembayaran berhasil, Anda akan diarahkan ke konfirmasi.</span></div>
            @endif
        </div>

        <div style="border-top:1px solid var(--clr-border); background:#fafafa; padding:1.5rem 2rem; text-align:center;">
            <button id="pay-button" class="btn-pay">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                Bayar Sekarang — Rp {{ number_format($order->payment_method === 'kombinasi' ? $order->amount_qris : $order->total_price, 0, ',', '.') }}
            </button>
            <div style="display:flex; align-items:center; justify-content:center; gap:.375rem; font-size:.78rem; color:var(--clr-muted); margin-top:.875rem;">
                🔒 Transaksi dienkripsi & diproses oleh Midtrans
            </div>
            <form method="POST" action="{{ route('donasi.reset-method', $order) }}" style="margin-top: 1rem;">
                @csrf
                <button type="submit" style="background:none; border:none; color:var(--clr-muted); font-size:.8rem; cursor:pointer; text-decoration:underline;">
                    ← Pilih metode pembayaran lain
                </button>
            </form>
        </div>
    </div>
@endif
@endsection

@push('scripts')
@if(is_null($order->payment_method))
<script>
function selectMethod(method) {
    // Remove selected class from all cards
    document.querySelectorAll('.method-card').forEach(card => {
        card.classList.remove('selected');
    });

    // Add selected class to chosen card
    const selectedCard = document.querySelector(`.method-card[data-method="${method}"]`);
    if (selectedCard) {
        selectedCard.classList.add('selected');
    }

    // Set value in hidden input and enable submit button
    document.getElementById('payment-method-input').value = method;
    document.getElementById('btn-submit-method').disabled = false;
}
</script>
@elseif($order->payment_method !== 'saldo')
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
@endif
@endpush
