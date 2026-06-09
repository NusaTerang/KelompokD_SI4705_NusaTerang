@extends('layouts.app')

@section('content')
<div class="w-full max-w-[1100px] mx-auto px-4 py-12 flex flex-col gap-8">

    {{-- Header --}}
    <div>
        <h1 class="text-on-surface text-4xl font-headline font-extrabold tracking-tight">Saldo Saya</h1>
        <p class="text-on-surface-variant text-base font-medium mt-1">Kelola saldo NusaTerang kamu untuk berdonasi.</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->has('refund'))
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3">
            ⚠️ {{ $errors->first('refund') }}
        </div>
    @endif

    {{-- Balance Card --}}
    <div class="rounded-3xl overflow-hidden shadow-lg">
        <div class="solar-gradient p-8 flex items-center justify-between gap-6">
            <div class="flex flex-col gap-1">
                <p class="text-deep-navy/70 text-sm font-bold uppercase tracking-widest">Saldo Aktif</p>
                <p class="text-deep-navy text-5xl font-headline font-extrabold">Rp {{ number_format($user->saldo, 0, ',', '.') }}</p>
                <p class="text-deep-navy/70 text-sm font-medium mt-1">Saldo hanya dapat digunakan untuk berdonasi di NusaTerang</p>
            </div>
            <a href="{{ route('donatur.topup.create') }}"
                class="shrink-0 inline-flex items-center gap-2 bg-deep-navy/15 hover:bg-deep-navy/25 text-deep-navy font-bold text-sm px-6 py-3.5 rounded-xl transition-colors">
                <span class="material-symbols-outlined text-[20px]">add_circle</span>
                Top Up Saldo
            </a>
        </div>
        <div class="bg-white px-8 py-6 flex items-start gap-4">
            <span class="material-symbols-outlined text-on-surface-variant">info</span>
            <ul class="flex flex-col gap-2 text-sm text-on-surface-variant">
                <li class="flex items-center gap-2"><span class="material-symbols-outlined text-[18px] text-green-600">check</span> Tidak ada masa kadaluarsa</li>
                <li class="flex items-center gap-2"><span class="material-symbols-outlined text-[18px] text-green-600">check</span> Tidak bisa ditarik tunai (withdraw) ke rekening pribadi</li>
                <li class="flex items-center gap-2"><span class="material-symbols-outlined text-[18px] text-green-600">check</span> Otomatis bertambah dari refund dana proyek</li>
            </ul>
        </div>
    </div>

    {{-- Riwayat Mutasi --}}
    <div class="flex flex-col gap-4">
        <h2 class="text-on-surface text-2xl font-headline font-bold">Riwayat Mutasi</h2>

        @if($mutasi->isEmpty())
            <div class="bg-white border border-surface-container rounded-2xl flex flex-col items-center justify-center py-16 px-6 text-center">
                <span class="material-symbols-outlined text-[40px] text-outline-variant mb-3">account_balance_wallet</span>
                <p class="text-on-surface font-bold text-lg">Belum ada riwayat saldo</p>
                <p class="text-on-surface-variant text-sm mt-1 max-w-md">Kamu belum memiliki saldo. Saldo akan otomatis masuk jika ada proyek yang dibatalkan.</p>
            </div>
        @else
            <div class="bg-white border border-surface-container rounded-2xl divide-y divide-surface-container overflow-hidden">
                @foreach($mutasi as $m)
                    @php $isCredit = $m->nominal >= 0; @endphp
                    <div class="flex items-center justify-between gap-4 p-5">
                        <div class="flex items-center gap-4">
                            <div class="w-11 h-11 rounded-full flex items-center justify-center {{ $isCredit ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                <span class="material-symbols-outlined text-[20px]">{{ $isCredit ? 'south_west' : 'north_east' }}</span>
                            </div>
                            <div class="flex flex-col">
                                <p class="text-on-surface font-bold capitalize">{{ $m->tipe }}</p>
                                <p class="text-on-surface-variant text-sm">{{ $m->keterangan ?? '-' }}</p>
                                <p class="text-on-surface-variant/70 text-xs mt-0.5">{{ $m->created_at->translatedFormat('d M Y, H:i') }} WIB</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold {{ $isCredit ? 'text-green-700' : 'text-red-700' }}">
                                {{ $isCredit ? '+' : '-' }} Rp {{ number_format(abs($m->nominal), 0, ',', '.') }}
                            </p>
                            <p class="text-on-surface-variant/70 text-xs">Saldo: Rp {{ number_format($m->saldo_sesudah, 0, ',', '.') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div>{{ $mutasi->links() }}</div>
        @endif
    </div>

</div>
@endsection
