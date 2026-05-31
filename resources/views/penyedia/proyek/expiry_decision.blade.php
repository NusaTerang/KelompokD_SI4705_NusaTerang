@extends('layouts.penyedia')

@section('title', 'Keputusan Proyek Berakhir')

@section('breadcrumbs')
    <a href="{{ route('vendor.dashboard') }}" class="hover:text-deep-navy">Dashboard</a>
    <span class="material-symbols-outlined text-xs mx-1">chevron_right</span>
    <a href="{{ route('vendor.proyek.index') }}" class="hover:text-deep-navy">Proyek Saya</a>
    <span class="material-symbols-outlined text-xs mx-1">chevron_right</span>
    <span class="text-deep-navy font-bold">Keputusan Proyek Berakhir</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <div>
        <h2 class="text-3xl font-extrabold text-deep-navy tracking-tight">Keputusan Proyek Berakhir</h2>
        <p class="text-on-surface-variant font-medium mt-1">Pilih apakah proyek akan diajukan refund atau dilanjutkan dengan dana yang sudah terkumpul.</p>
    </div>

    <div class="bg-white rounded-2xl p-8 shadow-sm border border-surface-container space-y-6">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-error/10 text-error flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined active-fill">warning</span>
            </div>
            <div>
                <p class="text-sm text-on-surface-variant font-bold uppercase tracking-widest mb-1">Proyek</p>
                <h3 class="text-2xl font-extrabold text-deep-navy">{{ $proyek->judul }}</h3>
                <p class="text-sm text-on-surface-variant font-medium mt-1">{{ $proyek->desa->nama_desa ?? '-' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-surface-container-low rounded-xl p-5">
                <p class="text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-2">Tanggal Berakhir Lama</p>
                <p class="text-lg font-extrabold text-deep-navy">{{ $proyek->expired_original_end_date?->format('d M Y') ?? '-' }}</p>
            </div>
            <div class="bg-surface-container-low rounded-xl p-5">
                <p class="text-[10px] font-black text-on-surface-variant uppercase tracking-widest mb-2">Diperpanjang Sampai</p>
                <p class="text-lg font-extrabold text-deep-navy">{{ $proyek->estimasi_selesai?->format('d M Y') ?? '-' }}</p>
            </div>
            <div class="bg-solar-gold/20 rounded-xl p-5">
                <p class="text-[10px] font-black text-deep-navy/70 uppercase tracking-widest mb-2">Dana Terkumpul</p>
                <p class="text-lg font-extrabold text-deep-navy">Rp {{ number_format($proyek->dana_terkumpul, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <form action="{{ route('vendor.proyek.expiry-decision', $penugasan->id_penugasan) }}" method="POST" class="bg-white rounded-2xl p-8 border border-error/30 shadow-sm space-y-5">
            @csrf
            <input type="hidden" name="decision" value="refund">
            <div class="w-12 h-12 rounded-xl bg-error text-white flex items-center justify-center">
                <span class="material-symbols-outlined active-fill">assignment_return</span>
            </div>
            <div>
                <h3 class="text-xl font-extrabold text-deep-navy">Ajukan Refund</h3>
                <p class="text-sm text-on-surface-variant font-medium mt-2">Status proyek akan berubah menjadi refund untuk diproses admin.</p>
            </div>
            <button type="submit" class="w-full py-4 bg-error text-white rounded-xl font-bold text-sm hover:bg-error/90 transition-all">
                Ajukan Refund
            </button>
        </form>

        <form action="{{ route('vendor.proyek.expiry-decision', $penugasan->id_penugasan) }}" method="POST" class="bg-white rounded-2xl p-8 border border-solar-gold/40 shadow-sm space-y-5">
            @csrf
            <input type="hidden" name="decision" value="continue">
            <div class="w-12 h-12 rounded-xl bg-solar-gold text-deep-navy flex items-center justify-center">
                <span class="material-symbols-outlined active-fill">play_circle</span>
            </div>
            <div>
                <h3 class="text-xl font-extrabold text-deep-navy">Lanjutkan Proyek</h3>
                <p class="text-sm text-on-surface-variant font-medium mt-2">Proyek diselesaikan dengan jumlah dana yang sudah terkumpul saat ini.</p>
            </div>
            <button type="submit" class="w-full py-4 bg-solar-gold text-deep-navy rounded-xl font-bold text-sm hover:brightness-105 transition-all">
                Lanjutkan Proyek
            </button>
        </form>
    </div>

    <a href="{{ route('vendor.proyek.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-deep-navy hover:underline">
        <span class="material-symbols-outlined text-sm">arrow_back</span>
        Kembali ke Proyek Saya
    </a>
</div>
@endsection
