@extends('layouts.app')

@section('title', 'Saldo Saya — NusaTerang')

@section('content')
    <section class="mx-auto w-full max-w-4xl px-4 py-8">

        {{-- ── Header ── --}}
        <div class="mb-6">
            <h1 class="font-headline text-3xl font-extrabold text-slate-900">Saldo Saya</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola saldo NusaTerang kamu untuk berdonasi.</p>
        </div>

        {{-- ── Card Saldo ── --}}
        <div class="mb-8 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="solar-gradient px-6 py-8 sm:px-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-white/80">Saldo Aktif</p>
                        <p class="mt-1 font-headline text-4xl font-extrabold tracking-tight text-white">
                            Rp {{ number_format($saldo, 0, ',', '.') }}
                        </p>
                        <p class="mt-2 text-xs text-white/60">Saldo hanya dapat digunakan untuk berdonasi di NusaTerang</p>
                    </div>
                    <a href="{{ route('donatur.topup.create') }}"
                       class="flex items-center gap-2 rounded-xl bg-white/20 px-5 py-3 text-sm font-bold text-white backdrop-blur-sm transition hover:bg-white/30">
                        <span class="material-symbols-outlined text-[20px]">add_circle</span>
                        Top Up Saldo
                    </a>
                </div>
            </div>

            {{-- ── Info rules saldo ── --}}
            <div class="flex items-start gap-3 border-t border-slate-100 bg-slate-50/50 px-6 py-4 sm:px-8">
                <span class="material-symbols-outlined mt-0.5 text-[18px] text-slate-400">info</span>
                <ul class="flex flex-col gap-2 text-xs text-slate-500">
                    <li class="flex items-center gap-1.5">
                        <span class="text-emerald-500 font-bold">✓</span>
                        <span>Tidak ada masa kadaluarsa</span>
                    </li>
                    <li class="flex items-center gap-1.5">
                        <span class="text-emerald-500 font-bold">✓</span>
                        <span>Tidak bisa ditarik tunai (withdraw) ke rekening pribadi</span>
                    </li>
                    <li class="flex items-center gap-1.5">
                        <span class="text-emerald-500 font-bold">✓</span>
                        <span>Otomatis bertambah dari refund dana proyek</span>
                    </li>
                </ul>
            </div>
        </div>

        {{-- ── Riwayat Mutasi ── --}}
        <div class="mb-4 flex items-center justify-between">
            <h2 class="font-headline text-lg font-bold text-slate-900">Riwayat Mutasi</h2>
            @if($mutasiList->total() > 0)
                <span class="text-xs text-slate-400">{{ $mutasiList->total() }} transaksi</span>
            @endif
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            @forelse($mutasiList as $mutasi)
                <div class="flex items-start gap-4 border-b border-slate-100 px-5 py-4 last:border-b-0 transition hover:bg-slate-50/50">
                    {{-- Icon tipe --}}
                    <span class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-full
                        @if($mutasi->tipe === 'refund')
                            bg-emerald-100 text-emerald-600
                        @elseif($mutasi->tipe === 'donasi')
                            bg-red-100 text-red-600
                        @else
                            bg-blue-100 text-blue-600
                        @endif
                    ">
                        <span class="material-symbols-outlined text-[20px]">
                            @if($mutasi->tipe === 'refund')
                                replay
                            @elseif($mutasi->tipe === 'donasi')
                                volunteer_activism
                            @else
                                account_balance_wallet
                            @endif
                        </span>
                    </span>

                    {{-- Detail --}}
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-headline text-sm font-bold text-slate-900">
                                @if($mutasi->tipe === 'refund')
                                    Refund Dana
                                @elseif($mutasi->tipe === 'donasi')
                                    Digunakan untuk Donasi
                                @else
                                    Top Up Saldo
                                @endif
                            </span>
                            <span class="rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide
                                @if($mutasi->tipe === 'refund')
                                    bg-emerald-100 text-emerald-700
                                @elseif($mutasi->tipe === 'donasi')
                                    bg-red-100 text-red-700
                                @else
                                    bg-blue-100 text-blue-700
                                @endif
                            ">
                                {{ ucfirst($mutasi->tipe) }}
                            </span>
                        </div>

                        @if($mutasi->proyek)
                            <p class="mt-1 text-sm text-slate-600">
                                <span class="material-symbols-outlined align-middle text-[14px] text-slate-400">folder</span>
                                {{ $mutasi->proyek->judul }}
                            </p>
                        @endif

                        @if($mutasi->keterangan)
                            <p class="mt-0.5 text-xs text-slate-400">{{ $mutasi->keterangan }}</p>
                        @endif

                        <p class="mt-1 text-xs text-slate-400">
                            {{ $mutasi->created_at->translatedFormat('d M Y, H:i') }}
                        </p>
                    </div>

                    {{-- Nominal --}}
                    <div class="shrink-0 text-right">
                        <p class="font-headline text-sm font-extrabold
                            @if($mutasi->isCredit())
                                text-emerald-600
                            @else
                                text-red-600
                            @endif
                        ">
                            {{ $mutasi->isCredit() ? '+' : '-' }}Rp {{ number_format($mutasi->nominal, 0, ',', '.') }}
                        </p>
                        <p class="mt-0.5 text-[11px] text-slate-400">
                            Saldo: Rp {{ number_format($mutasi->saldo_sesudah, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            @empty
                {{-- Empty state --}}
                <div class="px-6 py-16 text-center">
                    <span class="material-symbols-outlined text-5xl text-slate-300">account_balance_wallet</span>
                    <p class="mt-3 font-headline text-lg font-bold text-slate-700">Belum ada riwayat saldo</p>
                    <p class="mx-auto mt-1 max-w-sm text-sm text-slate-500">
                        Kamu belum memiliki saldo. Saldo akan otomatis masuk jika ada proyek yang dibatalkan.
                    </p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($mutasiList->hasPages())
            <div class="mt-6">
                {{ $mutasiList->links() }}
            </div>
        @endif

    </section>
@endsection
