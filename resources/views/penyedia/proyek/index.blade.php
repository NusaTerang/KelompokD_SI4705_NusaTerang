@extends('layouts.penyedia')

@section('title', 'Proyek Saya')

@section('breadcrumbs')
    <a href="{{ route('vendor.dashboard') }}" class="hover:text-deep-navy cursor-pointer">Dashboard</a>
    <span class="material-symbols-outlined text-xs mx-1">chevron_right</span>
    <span class="text-deep-navy font-bold">Proyek Saya</span>
@endsection

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <h2 class="text-3xl font-extrabold text-deep-navy tracking-tight">Proyek Saya</h2>
        <p class="text-on-surface-variant font-medium mt-1">Daftar semua permintaan proyek yang diberikan Admin kepada Anda.</p>
    </div>

    @if($penugasans->isEmpty())
        <div class="py-20 text-center bg-white rounded-2xl border border-dashed border-outline-variant">
            <span class="material-symbols-outlined text-[64px] text-outline-variant mb-4 block">folder_off</span>
            <h3 class="text-xl font-bold text-on-surface mb-2">Belum ada proyek</h3>
            <p class="text-on-surface-variant">Admin belum menugaskan proyek ke Anda.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($penugasans as $p)
            @php
                $proyek = $p->proyek;
                $statusColor = match($p->status_penugasan) {
                    'pending'  => 'bg-solar-gold/10 text-deep-navy',
                    'diterima' => 'bg-sustainability-green/10 text-sustainability-green',
                    'ditolak'  => 'bg-error/10 text-error',
                    default    => 'bg-surface-container text-on-surface-variant',
                };
                $statusLabel = match($p->status_penugasan) {
                    'pending'  => 'Menunggu Konfirmasi',
                    'diterima' => 'Diterima',
                    'ditolak'  => 'Ditolak',
                    default    => '-',
                };
                $detailStatus = $p->detail?->status;
            @endphp
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-surface-container hover:shadow-md transition-shadow flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start gap-4 flex-1 min-w-0">
                    <div class="w-12 h-12 rounded-xl bg-deep-navy/10 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-deep-navy">wb_sunny</span>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-bold text-deep-navy truncate">{{ $proyek->judul }}</h3>
                        <p class="text-sm text-on-surface-variant font-medium">
                            {{ $proyek->desa->nama_desa ?? '-' }}
                        </p>
                        <div class="flex flex-wrap gap-2 mt-2">
                            <span class="text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest {{ $statusColor }}">
                                {{ $statusLabel }}
                            </span>
                            @if($detailStatus === 'submitted')
                                <span class="text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest bg-sustainability-green/10 text-sustainability-green">
                                    Rincian Terkirim
                                </span>
                            @elseif($detailStatus === 'draft')
                                <span class="text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest bg-solar-gold/10 text-deep-navy">
                                    Draft Tersimpan
                                </span>
                            @else
                                <span class="text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest bg-surface-container text-on-surface-variant">
                                    Belum Diisi
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <a href="{{ route('vendor.proyek.show', $p->id_penugasan) }}"
                   class="shrink-0 px-5 py-2.5 bg-deep-navy text-white rounded-xl font-bold text-sm hover:bg-deep-navy/90 transition-all text-center">
                    Lihat &amp; Isi Rincian
                </a>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
