@php $step = 2; @endphp
@extends('layouts.donasi')

@section('title', 'Pembayaran')

@push('styles')
<style>
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #475569;
        text-decoration: none;
        font-weight: 500;
        margin-bottom: 2rem;
        font-size: 0.95rem;
    }
    .section-title {
        font-weight: 700;
        color: #1e3a8a;
        margin-bottom: 1.5rem;
        font-size: 1.25rem;
    }
    .payment-method-btn {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.25rem;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        background-color: #ffffff;
        margin-bottom: 2rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .payment-method-btn:hover {
        border-color: #cbd5e1;
        background-color: #f8fafc;
    }
    .payment-method-btn.selected {
        border-color: #3b82f6;
        background-color: #eff6ff;
    }
    .payment-method-left {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .payment-icon {
        background: white;
        width: 48px;
        height: 48px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .payment-icon svg { width: 24px; height: 24px; color: #1e3a8a; }
    .payment-text h4 { margin: 0; font-weight: 700; color: #1e293b; font-size: 1rem; }
    .payment-text p { margin: 0; color: #64748b; font-size: 0.85rem; }
    .radio-circle {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        border: 2px solid #cbd5e1;
        background: white;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .payment-method-btn.selected .radio-circle {
        border-color: #3b82f6;
    }
    .payment-method-btn.selected .radio-circle::after {
        content: '';
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: #3b82f6;
    }
    
    .summary-box {
        background: #f1f5f9;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 1rem;
        color: #475569;
        font-size: 0.95rem;
    }
    .summary-row:last-child { margin-bottom: 0; }
    .summary-row.total {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #cbd5e1;
        font-weight: 800;
        color: #1e293b;
        font-size: 1.1rem;
    }
    .total-value {
        color: #1e3a8a;
    }
    .disclaimer {
        text-align: center;
        font-size: 0.8rem;
        color: #64748b;
        margin-top: 1.5rem;
        line-height: 1.5;
    }
</style>
@endpush

@section('content')
<div class="card">
    <a href="{{ route('donasi.create', ['proyek' => $order->proyek_id]) }}" class="back-link">
        ← Kembali
    </a>

    <div class="section-title">Metode Pembayaran</div>

    <div class="payment-method-btn selected" id="payment-midtrans" onclick="this.classList.toggle('selected');">
        <div class="payment-method-left">
            <div class="payment-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                    <path d="M7 7h.01M7 11h.01M11 7h.01M11 11h.01M7 15h.01M11 15h.01M15 7h.01M15 11h.01M15 15h.01"/>
                </svg>
            </div>
            <div class="payment-text">
                <h4>QRIS</h4>
                <p>Scan All Digital Wallets</p>
            </div>
        </div>
        <div class="radio-circle"></div>
    </div>

    @php
        $biayaLayanan = 0; // Sesuaikan jika ada biaya layanan
        $totalBayar = $order->total_price + $biayaLayanan;
    @endphp

    <div class="summary-box">
        <div class="summary-row">
            <span>Jumlah Donasi</span>
            <span style="font-weight:700; color:#1e293b;">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
        </div>
        @if($biayaLayanan > 0)
        <div class="summary-row">
            <span>Biaya Layanan</span>
            <span style="font-weight:700; color:#1e293b;">Rp {{ number_format($biayaLayanan, 0, ',', '.') }}</span>
        </div>
        @endif
        <div class="summary-row total">
            <span>Total Pembayaran</span>
            <span class="total-value">Rp {{ number_format($totalBayar, 0, ',', '.') }}</span>
        </div>
    </div>

    <button type="button" class="btn-primary" id="pay-button">
        🔒 Bayar Sekarang
    </button>

    <div class="disclaimer">
        Pembayaran Anda diproses secara aman melalui gerbang pembayaran terenkripsi. Dengan menekan tombol di atas, Anda menyetujui Syarat & Ketentuan kami.
    </div>
</div>
@endsection

@push('scripts')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
    document.getElementById('pay-button').onclick = function(){
        snap.pay('{{ $snapToken }}', {
            onSuccess: function(result){
                window.location.href = "{{ route('donasi.status', $order) }}";
            },
            onPending: function(result){
                window.location.href = "{{ route('donasi.status', $order) }}";
            },
            onError: function(result){
                window.location.href = "{{ route('donasi.status', $order) }}";
            },
            onClose: function(){
                // user closed popup
            }
        });
    };
</script>
@endpush
