@php $step = 3; @endphp
@extends('layouts.donasi')

@section('title', 'Status Pembayaran')

@push('styles')
<style>
    .status-container {
        text-align: center;
        max-width: 500px;
        margin: 0 auto;
    }
    .status-icon {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        color: white;
    }
    .status-icon svg { width: 32px; height: 32px; }
    
    .status-pending .status-icon { background-color: #94a3b8; }
    .status-success .status-icon { background-color: #10b981; }
    .status-failed .status-icon { background-color: #ef4444; }
    
    .status-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: #1e3a8a;
        margin-bottom: 0.75rem;
    }
    .status-message {
        color: #475569;
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 2rem;
    }
    
    .summary-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.5rem;
        text-align: left;
        margin-bottom: 2rem;
    }
    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.75rem;
        font-size: 0.9rem;
    }
    .summary-row:last-child { margin-bottom: 0; }
    .summary-label { color: #64748b; }
    .summary-val { color: #1e293b; font-weight: 600; text-align: right; }
    .summary-val.highlight { color: #10b981; font-weight: 800; font-size: 1.05rem; }
    
    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-bottom: 3rem;
    }
    
    .stats-container {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin-top: 2rem;
    }
    .stat-card {
        background: #f1f5f9;
        border-radius: 12px;
        padding: 1.25rem;
        text-align: left;
    }
    .stat-icon {
        color: #10b981;
        margin-bottom: 0.5rem;
    }
    .stat-icon svg { width: 20px; height: 20px; }
    .stat-val {
        font-size: 1.25rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 0.25rem;
    }
    .stat-label {
        font-size: 0.75rem;
        color: #64748b;
    }

    @media (max-width: 600px) {
        .stats-container { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="card">
    <div class="status-container 
        @if($order->isPending()) status-pending 
        @elseif($order->isSuccess()) status-success 
        @else status-failed @endif">
        
        <div class="status-icon">
            @if($order->isPending())
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="1"></circle>
                    <circle cx="19" cy="12" r="1"></circle>
                    <circle cx="5" cy="12" r="1"></circle>
                </svg>
            @elseif($order->isSuccess())
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 6L9 17l-5-5"></path>
                </svg>
            @else
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            @endif
        </div>

        <div class="status-title">
            @if($order->isPending()) Menunggu Pembayaran
            @elseif($order->isSuccess()) Pembayaran Berhasil
            @elseif($order->isExpired()) Pembayaran Kadaluarsa
            @else Pembayaran Dibatalkan @endif
        </div>
        
        <div class="status-message">
            Terima kasih telah berkontribusi dalam menerangi masa depan nusantara melalui energi berkelanjutan.
        </div>

        <div class="summary-box">
            <div class="summary-row">
                <span class="summary-label">Jumlah Donasi</span>
                <span class="summary-val highlight">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">ID Transaksi</span>
                <span class="summary-val">{{ $order->number }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Proyek</span>
                <span class="summary-val">Pembangkit Listrik Tenaga Surya (PLTS)</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Desa</span>
                <span class="summary-val">Sumba Timur, NTT</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Waktu</span>
                <span class="summary-val">{{ $order->created_at->format('d M Y, H:i') }} WIB</span>
            </div>
        </div>

        <div class="action-buttons">
            <a href="#" class="btn-primary">Lihat Progres Proyek</a>
            <a href="{{ url('/') }}" class="btn-outline">Kembali ke Beranda</a>
        </div>
    </div>

    <!-- Static Impact Stats -->
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"></path>
                </svg>
            </div>
            <div class="stat-val">12.5 kWp</div>
            <div class="stat-label">Total Energi Terpasang</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
            <div class="stat-val">250 KK</div>
            <div class="stat-label">Penerima Manfaat</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2c-5.33 0-8 6-8 10a8 8 0 1 0 16 0c0-4-2.67-10-8-10z"></path>
                </svg>
            </div>
            <div class="stat-val">4.2 Ton</div>
            <div class="stat-label">Emisi CO2 Tereduksi</div>
        </div>
    </div>
</div>
@endsection
