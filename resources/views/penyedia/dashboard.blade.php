@extends('layouts.penyedia')

@section('title', 'Dashboard Vendor')

@section('breadcrumbs')
    <span class="text-deep-navy font-bold font-body">Dashboard</span>
@endsection

@section('content')
@php
    $penyedia = auth()->user()->penyedia;
    $totalProyek = \App\Models\PenugasanProyek::where('id_penyedia', $penyedia?->id ?? 0)->count();
    $pending = \App\Models\PenugasanProyek::where('id_penyedia', $penyedia?->id ?? 0)->where('status_penugasan', 'pending')->count();
@endphp

<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <h2 class="text-3xl font-extrabold text-deep-navy tracking-tight">
            Selamat datang, {{ auth()->user()->nama }}
        </h2>
        <p class="text-on-surface-variant font-medium mt-1">
            {{ $penyedia?->nama ?? 'Penyedia Energi' }} &mdash; Portal Vendor NusaTerang
        </p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-10">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-surface-container">
            <div class="flex items-center justify-between mb-4">
                <p class="text-xs font-bold text-on-surface-variant uppercase tracking-widest">Total Proyek</p>
                <span class="material-symbols-outlined text-deep-navy">folder_special</span>
            </div>
            <p class="text-4xl font-extrabold text-deep-navy">{{ $totalProyek }}</p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-surface-container">
            <div class="flex items-center justify-between mb-4">
                <p class="text-xs font-bold text-on-surface-variant uppercase tracking-widest">Menunggu Aksi</p>
                <span class="material-symbols-outlined text-solar-gold">schedule</span>
            </div>
            <p class="text-4xl font-extrabold text-solar-gold">{{ $pending }}</p>
        </div>
        <div class="bg-solar-gold rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <p class="text-xs font-bold text-deep-navy/70 uppercase tracking-widest">Spesialisasi</p>
                <span class="material-symbols-outlined text-deep-navy">wb_sunny</span>
            </div>
            <p class="text-lg font-extrabold text-deep-navy">
                {{ match($penyedia?->spesialisasi) {
                    'panel_surya' => 'Panel Surya',
                    'mikro_hidro' => 'Mikro Hidro',
                    'biogas' => 'Biogas',
                    'hybrid_solar_baterai' => 'Hybrid',
                    default => '-'
                } }}
            </p>
        </div>
    </div>

    @if($pending > 0)
        <div class="bg-solar-gold/10 rounded-2xl p-5 flex items-center justify-between mb-8 border border-solar-gold/20">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-solar-gold flex items-center justify-center text-deep-navy">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">notifications</span>
                </div>
                <div>
                    <p class="text-sm text-deep-navy font-bold">Kamu punya {{ $pending }} permintaan proyek baru dari Admin</p>
                    <p class="text-xs text-deep-navy/70 font-medium">Segera isi rincian teknis agar proyek dapat dipublikasikan.</p>
                </div>
            </div>
            <a href="{{ route('vendor.proyek.index') }}"
               class="px-5 py-2.5 bg-deep-navy text-white rounded-xl font-bold text-sm hover:bg-deep-navy/90 transition-all shrink-0">
                Lihat Sekarang
            </a>
        </div>
    @endif

    <div class="bg-white rounded-2xl p-8 shadow-sm border border-surface-container">
        <h3 class="text-lg font-bold text-deep-navy mb-4">Aksi Cepat</h3>
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('vendor.proyek.index') }}"
               class="flex items-center gap-2 px-5 py-3 bg-deep-navy text-white rounded-xl font-bold text-sm hover:bg-deep-navy/90 transition-all">
                <span class="material-symbols-outlined text-[18px]">folder_open</span>
                Lihat Semua Proyek
            </a>
        </div>
    </div>
</div>
@endsection
