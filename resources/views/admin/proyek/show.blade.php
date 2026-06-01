@extends('layouts.admin')

@section('title', 'Detail Proyek')
@section('page_heading', 'Detail Proyek')

@section('breadcrumbs')
    <span>Dashboard</span>
    <span class="mx-1">›</span>
    <a href="{{ route('proyek.kelola') }}" class="hover:text-nt-navy">Kelola Proyek</a>
    <span class="mx-1">›</span>
    <span class="font-semibold text-nt-navy">Detail</span>
@endsection

@section('content')
@php
    $penugasan = $proyek->penugasan->first();
    $detail = $penugasan?->detail;
    $energyLabels = [
        'panel_surya' => 'Panel Surya',
        'mikro_hidro' => 'Mikro Hidro',
        'biogas' => 'Biogas',
        'hybrid_solar_baterai' => 'Hybrid Solar + Baterai',
    ];
    $jenisEnergi = $energyLabels[$proyek->jenis_energi] ?? ucfirst(str_replace('_', ' ', $proyek->jenis_energi ?? '-'));
@endphp

<div class="max-w-6xl mx-auto space-y-8">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-extrabold text-nt-navy tracking-tight">{{ $proyek->judul }}</h2>
            <p class="text-sm text-slate-500 mt-1">Detail proyek dan rincian teknis dari vendor.</p>
        </div>
        <a href="{{ route('proyek.kelola') }}" class="inline-flex items-center gap-2 rounded-lg bg-white border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 hover:text-slate-900 transition-all">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Kembali
        </a>
    </div>

    <div class="grid grid-cols-12 gap-8 items-start">
        {{-- Kolom Kiri --}}
        <div class="col-span-12 lg:col-span-7 space-y-8">
            <section class="bg-white rounded-2xl p-8 shadow-sm border border-slate-200">
                <h3 class="text-lg font-bold text-slate-900 mb-6">Informasi Proyek</h3>

                @if($proyek->fotos->isNotEmpty())
                    <div class="grid grid-cols-2 gap-3 mb-6">
                        @foreach($proyek->fotos->take(4) as $foto)
                            @php
                                $fotoUrl = str_starts_with($foto->path, 'http') ? $foto->path : asset('storage/' . $foto->path);
                            @endphp
                            <div class="h-40 overflow-hidden rounded-xl bg-slate-100">
                                <img src="{{ $fotoUrl }}" alt="Foto Proyek" class="w-full h-full object-cover">
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="bg-slate-50 rounded-xl p-4">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Desa</p>
                        <p class="font-bold text-slate-900">{{ $proyek->desa->nama_desa ?? '-' }}</p>
                        <p class="text-slate-500">{{ $proyek->desa->kabupaten ?? '-' }}, {{ $proyek->desa->provinsi ?? '-' }}</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-4">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Jenis Energi</p>
                        <p class="font-bold text-slate-900">{{ $jenisEnergi }}</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-4">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Target Dana</p>
                        <p class="font-bold text-slate-900">Rp {{ number_format($proyek->target_dana ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-4">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Status</p>
                        <p class="font-bold text-slate-900">{{ ucfirst(str_replace('_', ' ', $proyek->status ?? '-')) }}</p>
                    </div>
                </div>

                @if($proyek->deskripsi)
                    <div class="mt-6">
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Deskripsi</p>
                        <p class="text-sm text-slate-500 leading-relaxed">{{ $proyek->deskripsi }}</p>
                    </div>
                @endif
            </section>

            {{-- Rincian Vendor --}}
            <section class="bg-white rounded-2xl p-8 shadow-sm border border-slate-200">
                <h3 class="text-lg font-bold text-slate-900 mb-6">Rincian Vendor</h3>
                @if($detail)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm mb-6">
                        <div class="bg-slate-50 rounded-xl p-4">
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Kapasitas Daya</p>
                            <p class="font-bold text-slate-900">{{ $detail->kapasitas_daya }} {{ $detail->satuan_daya }}</p>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-4">
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Target Dana Vendor</p>
                            <p class="font-bold text-slate-900">Rp {{ number_format($detail->target_dana ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-4">
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Durasi Pengerjaan</p>
                            <p class="font-bold text-slate-900">{{ $detail->durasi_minggu }} minggu</p>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-4">
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Status Rincian</p>
                            <p class="font-bold text-slate-900">{{ ucfirst(str_replace('_', ' ', $detail->status ?? '-')) }}</p>
                        </div>
                    </div>

                    @if(!empty($detail->cost_breakdown))
                        <div class="mb-6">
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-3">Cost Breakdown</p>
                            <div class="divide-y divide-slate-100 rounded-xl border border-slate-200 overflow-hidden">
                                @foreach($detail->cost_breakdown as $item)
                                    <div class="flex items-center justify-between gap-4 bg-white px-4 py-3 text-sm">
                                        <span class="font-semibold text-slate-900">{{ $item['nama'] ?? '-' }}</span>
                                        <span class="font-bold text-nt-navy">Rp {{ number_format($item['nominal'] ?? 0, 0, ',', '.') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($detail->catatan_teknis)
                        <div>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Catatan Teknis</p>
                            <p class="text-sm text-slate-500 leading-relaxed bg-slate-50 rounded-xl p-4">{{ $detail->catatan_teknis }}</p>
                        </div>
                    @endif
                @else
                    <div class="rounded-2xl bg-slate-50 p-8 text-center">
                        <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">pending_actions</span>
                        <p class="font-bold text-slate-900">Rincian vendor belum tersedia</p>
                        <p class="text-sm text-slate-500 mt-1">Vendor belum mengirim rincian teknis proyek.</p>
                    </div>
                @endif
            </section>
        </div>

        {{-- Kolom Kanan --}}
        <aside class="col-span-12 lg:col-span-5 space-y-8">
            <section class="bg-white rounded-2xl p-8 shadow-sm border border-slate-200">
                <h3 class="text-lg font-bold text-slate-900 mb-6">Penyedia Terpilih</h3>
                @if($proyek->penyedia)
                    <div class="space-y-3 text-sm">
                        <p class="text-xl font-bold text-slate-900">{{ $proyek->penyedia->nama }}
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $proyek->penyedia->status === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($proyek->penyedia->status) }}
                            </span>
                        </p>
                        <p class="text-slate-500">{{ ucfirst(str_replace('_', ' ', $proyek->penyedia->spesialisasi ?? '-')) }}</p>
                        @if($proyek->penyedia->email)
                            <p class="text-slate-500">{{ $proyek->penyedia->email }}</p>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-slate-500">Belum ada penyedia dipilih.</p>
                @endif
            </section>
        </aside>
    </div>
</div>
@endsection
