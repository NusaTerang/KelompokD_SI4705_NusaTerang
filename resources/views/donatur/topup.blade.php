@extends('layouts.app')

@section('content')
<div class="w-full max-w-[640px] mx-auto px-4 py-12 flex flex-col gap-8">

    <div>
        <a href="{{ route('donatur.saldo') }}" class="inline-flex items-center gap-1 text-sm font-bold text-on-surface-variant hover:text-deep-navy mb-3">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali ke Saldo
        </a>
        <h1 class="text-on-surface text-4xl font-headline font-extrabold tracking-tight">Top Up Saldo</h1>
        <p class="text-on-surface-variant text-base font-medium mt-1">Isi saldo NusaTerang via QRIS. Saldo dapat dipakai untuk berdonasi.</p>
    </div>

    @if($errors->has('payment'))
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3">⚠️ {{ $errors->first('payment') }}</div>
    @endif

    <form action="{{ route('donatur.topup.store') }}" method="POST" class="bg-white rounded-2xl p-8 border border-surface-container shadow-lg flex flex-col gap-6">
        @csrf

        <div class="flex items-center justify-between bg-solar-gold/15 rounded-xl px-5 py-4">
            <span class="text-sm font-bold text-deep-navy uppercase tracking-wide">Saldo Saat Ini</span>
            <span class="text-xl font-extrabold text-deep-navy">Rp {{ number_format($user->saldo, 0, ',', '.') }}</span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            @foreach([50000, 100000, 250000, 500000] as $chip)
                <button type="button" onclick="document.getElementById('amount').value={{ $chip }}"
                    class="py-3 rounded-xl border border-outline-variant text-on-surface font-bold hover:bg-surface-container-low transition-colors text-sm">
                    Rp {{ number_format($chip / 1000, 0) }}k
                </button>
            @endforeach
        </div>

        <div class="flex flex-col gap-2">
            <label for="amount" class="text-on-surface-variant text-sm font-bold">Nominal Top Up</label>
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant font-bold text-sm">Rp</span>
                <input id="amount" name="amount" type="number" min="10000" step="1000" value="{{ old('amount') }}" placeholder="0"
                    class="w-full bg-surface-container-low rounded-xl py-4 pl-12 pr-4 outline-none text-right font-bold text-on-surface focus:ring-2 focus:ring-solar-gold border-none {{ $errors->has('amount') ? 'ring-2 ring-red-400' : '' }}" required />
            </div>
            @error('amount')<span class="text-red-600 text-xs">⚠ {{ $message }}</span>@enderror
            <p class="text-on-surface-variant text-xs">Minimum Rp 10.000. Pembayaran via QRIS (Midtrans).</p>
        </div>

        <button type="submit" class="w-full bg-primary-container text-on-primary-fixed font-headline font-extrabold text-lg py-4 rounded-xl shadow-md hover:opacity-90 transition-all flex items-center justify-center gap-2">
            <span class="material-symbols-outlined">qr_code_2</span>
            Lanjut Bayar QRIS
        </button>
    </form>

</div>
@endsection
