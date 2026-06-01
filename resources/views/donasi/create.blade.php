@php $step = 1; @endphp
@extends('layouts.donasi')

@section('title', 'Jumlah Donasi')

@push('styles')
<style>
    .project-header {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }
    .project-img {
        width: 64px;
        height: 64px;
        background-color: #1e293b;
        border-radius: 12px;
        object-fit: cover;
    }
    .project-title {
        font-weight: 700;
        font-size: 1.1rem;
        color: #1e3a8a;
        margin-bottom: 0.25rem;
    }
    .project-location {
        color: #64748b;
        font-size: 0.9rem;
    }
    .section-title {
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 1rem;
        font-size: 1.05rem;
    }
    .amount-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin-bottom: 1rem;
    }
    .amount-chip {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        padding: 0.875rem;
        text-align: center;
        border-radius: 30px;
        font-weight: 600;
        color: #334155;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 0.95rem;
    }
    .amount-chip:hover {
        border-color: #facc15;
    }
    .amount-chip.selected {
        background-color: #facc15;
        border-color: #facc15;
        color: #1e293b;
    }
    .custom-amount {
        display: flex;
        background: #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 2.5rem;
    }
    .custom-amount-prefix {
        padding: 1rem 1.25rem;
        font-weight: 700;
        color: #475569;
        display: flex;
        align-items: center;
    }
    .custom-amount input {
        flex: 1;
        border: none;
        background: transparent;
        padding: 1rem;
        font-size: 1rem;
        font-weight: 500;
        color: #1e293b;
        outline: none;
    }
    .custom-amount input::placeholder {
        color: #94a3b8;
    }
    textarea.form-control {
        background-color: #e2e8f0;
        border: none;
        min-height: 100px;
        resize: vertical;
        margin-bottom: 2rem;
    }
</style>
@endpush

@section('content')
<div class="card">
    <div class="project-header">
        <img src="{{ $proyek->fotos->first() ? asset('storage/' . $proyek->fotos->first()->path_foto) : 'https://placehold.co/64x64/1e293b/FFFFFF?text=NT' }}" class="project-img" alt="Foto Proyek">
        <div>
            <div class="project-title">{{ $proyek->judul }}</div>
            <div class="project-location">{{ $proyek->desa->kabupaten ?? '-' }}, {{ $proyek->desa->provinsi ?? '-' }}</div>
        </div>
    </div>

    <form method="POST" action="{{ route('donasi.store', $proyek->id) }}" id="form-donasi">
        @csrf
        
        <!-- Hidden inputs required by controller -->
        <input type="hidden" name="donatur_name" value="{{ $user->name ?? 'Donatur' }}">
        <input type="hidden" name="donatur_email" value="{{ $user->email ?? 'donatur@example.com' }}">

        <div class="section-title">Pilih Jumlah Donasi</div>
        
        <div class="amount-grid">
            <div class="amount-chip" data-value="25000">Rp 25.000</div>
            <div class="amount-chip" data-value="50000">Rp 50.000</div>
            <div class="amount-chip" data-value="100000">Rp 100.000</div>
            <div class="amount-chip" data-value="250000">Rp 250.000</div>
            <div class="amount-chip" data-value="500000">Rp 500.000</div>
        </div>

        <div class="custom-amount">
            <div class="custom-amount-prefix">Rp</div>
            <input type="number" id="total_price" name="total_price" placeholder="Jumlah lainnya" min="10000" step="1000" required>
        </div>

        <div class="section-title">Pesan untuk Desa (Opsional)</div>
        <textarea name="pesan" class="form-control" placeholder="Tuliskan kata-kata penyemangat untuk warga desa..."></textarea>

        <button type="submit" class="btn-primary" id="btn-submit">
            Lanjut ke Pembayaran ➔
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script>
    const chips = document.querySelectorAll('.amount-chip');
    const inputAmount = document.getElementById('total_price');

    chips.forEach(chip => {
        chip.addEventListener('click', () => {
            chips.forEach(c => c.classList.remove('selected'));
            chip.classList.add('selected');
            inputAmount.value = chip.dataset.value;
        });
    });

    inputAmount.addEventListener('input', () => {
        chips.forEach(c => c.classList.remove('selected'));
        const val = inputAmount.value;
        chips.forEach(c => {
            if (c.dataset.value === val) {
                c.classList.add('selected');
            }
        });
    });
    
    document.getElementById('form-donasi').addEventListener('submit', function() {
        const btn = document.getElementById('btn-submit');
        btn.disabled = true;
        btn.innerHTML = 'Memproses...';
    });
</script>
@endpush
