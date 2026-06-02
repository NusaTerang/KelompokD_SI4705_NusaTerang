@php $step = 1; @endphp
@extends('layouts.donasi')

@section('title', 'Form Donasi')

@section('content')
<div class="card">
    <div class="card-header">
        <h1>💚 Donasi untuk NusaTerang</h1>
        <p>Bantu kami menerangi desa-desa terpencil dengan energi terbarukan.</p>
    </div>
    <div class="card-body">

        <form method="POST" action="{{ route('donasi.store') }}" id="form-donasi">
            @csrf
            <input type="hidden" name="proyek_id" value="{{ $proyek->id ?? old('proyek_id') }}">

            {{-- Nama Donatur --}}
            <div class="form-group">
                <label class="form-label" for="donatur_name">
                    Nama Lengkap <span class="required">*</span>
                </label>
                <input
                    type="text"
                    id="donatur_name"
                    name="donatur_name"
                    class="form-control @error('donatur_name') is-invalid @enderror"
                    placeholder="Nama Anda"
                    value="{{ old('donatur_name', auth()->user()->name) }}"
                    required
                    autocomplete="name"
                >
                @error('donatur_name')
                    <span class="invalid-feedback">⚠ {{ $message }}</span>
                @enderror
            </div>

            {{-- Email --}}
            <div class="form-group">
                <label class="form-label" for="donatur_email">
                    Email <span class="required">*</span>
                </label>
                <input
                    type="email"
                    id="donatur_email"
                    name="donatur_email"
                    class="form-control @error('donatur_email') is-invalid @enderror"
                    placeholder="email@contoh.com"
                    value="{{ old('donatur_email', auth()->user()->email) }}"
                    required
                    autocomplete="email"
                >
                @error('donatur_email')
                    <span class="invalid-feedback">⚠ {{ $message }}</span>
                @enderror
                <p class="form-hint">📧 Bukti donasi akan dikirim ke email ini.</p>
            </div>

            {{-- Nomor HP --}}
            <div class="form-group">
                <label class="form-label" for="donatur_phone">Nomor HP <span style="color:var(--clr-muted);font-weight:400">(opsional)</span></label>
                <input
                    type="tel"
                    id="donatur_phone"
                    name="donatur_phone"
                    class="form-control @error('donatur_phone') is-invalid @enderror"
                    placeholder="08xxxxxxxxxx"
                    value="{{ old('donatur_phone') }}"
                    autocomplete="tel"
                >
                @error('donatur_phone')
                    <span class="invalid-feedback">⚠ {{ $message }}</span>
                @enderror
            </div>

            {{-- Jumlah Donasi --}}
            <div class="form-group">
                <label class="form-label" for="total_price">
                    Jumlah Donasi <span class="required">*</span>
                </label>

                {{-- Quick-pick chips --}}
                <div class="amount-chips" id="amount-chips">
                    <span class="chip" data-value="25000">Rp 25.000</span>
                    <span class="chip" data-value="50000">Rp 50.000</span>
                    <span class="chip" data-value="100000">Rp 100.000</span>
                    <span class="chip" data-value="200000">Rp 200.000</span>
                    <span class="chip" data-value="500000">Rp 500.000</span>
                </div>

                <div class="input-prefix-wrap @error('total_price') is-invalid @enderror">
                    <span class="input-prefix">Rp</span>
                    <input
                        type="number"
                        id="total_price"
                        name="total_price"
                        class="form-control"
                        placeholder="Atau masukkan nominal lain..."
                        value="{{ old('total_price') }}"
                        min="10000"
                        step="1000"
                        required
                    >
                </div>
                @error('total_price')
                    <span class="invalid-feedback">⚠ {{ $message }}</span>
                @enderror
                <p class="form-hint">Minimum donasi Rp 10.000</p>
            </div>

            {{-- Pesan --}}
            <div class="form-group">
                <label class="form-label" for="pesan">
                    Pesan / Doa <span style="color:var(--clr-muted);font-weight:400">(opsional)</span>
                </label>
                <textarea
                    id="pesan"
                    name="pesan"
                    class="form-control @error('pesan') is-invalid @enderror"
                    placeholder="Tulis semangat atau doa untuk warga desa..."
                    maxlength="500"
                >{{ old('pesan') }}</textarea>
                @error('pesan')
                    <span class="invalid-feedback">⚠ {{ $message }}</span>
                @enderror
                <p class="form-hint">Maksimal 500 karakter.</p>
            </div>

            {{-- Info QRIS --}}
            <div style="background:var(--clr-primary-lt); border:1px solid #a7f3d0; border-radius:var(--radius-sm); padding:1rem 1.25rem; margin-bottom:1.25rem; display:flex; align-items:flex-start; gap:.75rem;">
                <span style="font-size:1.4rem; line-height:1;">📱</span>
                <div>
                    <p style="font-weight:700; color:var(--clr-primary-dk); font-size:.9rem;">Pembayaran via QRIS</p>
                    <p style="font-size:.82rem; color:var(--clr-muted); margin-top:.2rem;">Scan QR code menggunakan aplikasi dompet digital apa pun — GoPay, OVO, Dana, ShopeePay, BCA Mobile, dll.</p>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg" id="btn-submit">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                Lanjutkan ke Pembayaran
            </button>
        </form>

    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    const chips     = document.querySelectorAll('.chip');
    const amountInput = document.getElementById('total_price');
    const form      = document.getElementById('form-donasi');
    const btnSubmit = document.getElementById('btn-submit');

    // Chip selection
    chips.forEach(chip => {
        chip.addEventListener('click', () => {
            chips.forEach(c => c.classList.remove('selected'));
            chip.classList.add('selected');
            amountInput.value = chip.dataset.value;
        });
    });

    // Remove chip selection when user types manually
    amountInput.addEventListener('input', () => {
        const val = parseInt(amountInput.value);
        chips.forEach(c => {
            c.classList.toggle('selected', parseInt(c.dataset.value) === val);
        });
    });

    // Disable submit button on submit to prevent double submit
    form.addEventListener('submit', () => {
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = `
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10" opacity=".25"/>
                <path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round">
                    <animateTransform attributeName="transform" type="rotate" from="0 12 12" to="360 12 12" dur=".8s" repeatCount="indefinite"/>
                </path>
            </svg>
            Memproses...
        `;
    });

    // Pre-select chip if old value matches
    const oldVal = amountInput.value;
    if (oldVal) {
        chips.forEach(c => {
            if (c.dataset.value === oldVal) c.classList.add('selected');
        });
    }
})();
</script>
@endpush
