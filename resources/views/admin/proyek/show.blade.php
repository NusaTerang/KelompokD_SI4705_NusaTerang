@extends('layouts.admin')

@section('breadcrumbs')
    <span class="hover:text-secondary cursor-pointer">Dashboard</span>
    <span class="material-symbols-outlined text-xs mx-1">chevron_right</span>
    <a href="{{ route('proyek.kelola') }}" class="hover:text-secondary">Kelola Proyek</a>
    <span class="material-symbols-outlined text-xs mx-1">chevron_right</span>
    <span class="text-secondary font-bold">Detail Proyek</span>
@endsection

@section('page_heading', 'Detail Proyek')

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
            <h2 class="text-3xl font-extrabold text-on-surface tracking-tight">{{ $proyek->judul }}</h2>
            <p class="text-sm text-on-surface-variant mt-1">Detail proyek dan rincian teknis dari vendor.</p>
        </div>
        <a href="{{ route('proyek.kelola') }}" class="px-5 py-3 rounded-xl bg-surface-container text-on-surface-variant font-bold text-sm hover:bg-surface-container-high transition-colors">
            Kembali
        </a>
    </div>

    <div class="grid grid-cols-12 gap-8 items-start">
        <div class="col-span-12 lg:col-span-7 space-y-8">
            <section class="bg-surface-container-lowest rounded-2xl p-8 shadow-sm border border-surface-container">
                <h3 class="text-lg font-bold text-on-surface mb-6">Informasi Proyek</h3>

                @if($proyek->fotos->isNotEmpty())
                    <div class="grid grid-cols-2 gap-3 mb-6">
                        @foreach($proyek->fotos->take(4) as $foto)
                            @php
                                $fotoUrl = str_starts_with($foto->path, 'http://') || str_starts_with($foto->path, 'https://')
                                    ? $foto->path
                                    : asset('storage/' . $foto->path);
                            @endphp
                            <div class="h-40 overflow-hidden rounded-xl bg-surface-container-low">
                                <img src="{{ $fotoUrl }}" alt="Foto Proyek" class="w-full h-full object-cover">
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="bg-surface-container-low rounded-xl p-4">
                        <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Desa</p>
                        <p class="font-bold text-on-surface">{{ $proyek->desa->nama_desa ?? '-' }}</p>
                        <p class="text-on-surface-variant">{{ $proyek->desa->kabupaten ?? '-' }}, {{ $proyek->desa->provinsi ?? '-' }}</p>
                    </div>
                    <div class="bg-surface-container-low rounded-xl p-4">
                        <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Jenis Energi</p>
                        <p class="font-bold text-on-surface">{{ $jenisEnergi }}</p>
                    </div>
                    <div class="bg-surface-container-low rounded-xl p-4">
                        <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Target Dana Admin</p>
                        <p class="font-bold text-on-surface">Rp {{ number_format($proyek->target_dana ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-surface-container-low rounded-xl p-4">
                        <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Status</p>
                        <p class="font-bold text-on-surface">{{ ucfirst(str_replace('_', ' ', $proyek->status ?? '-')) }}</p>
                    </div>
                </div>

                @if($proyek->deskripsi)
                    <div class="mt-6">
                        <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-2">Deskripsi</p>
                        <p class="text-sm text-on-surface-variant leading-relaxed">{{ $proyek->deskripsi }}</p>
                    </div>
                @endif
            </section>

            <section class="bg-surface-container-lowest rounded-2xl p-8 shadow-sm border border-surface-container">
                <h3 class="text-lg font-bold text-on-surface mb-6">Rincian Vendor</h3>

                @if($detail)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm mb-6">
                        <div class="bg-surface-container-low rounded-xl p-4">
                            <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Kapasitas Daya</p>
                            <p class="font-bold text-on-surface">{{ $detail->kapasitas_daya }} {{ $detail->satuan_daya }}</p>
                        </div>
                        <div class="bg-surface-container-low rounded-xl p-4">
                            <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Target Dana Vendor</p>
                            <p class="font-bold text-on-surface">Rp {{ number_format($detail->target_dana ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-surface-container-low rounded-xl p-4">
                            <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Durasi Pengerjaan</p>
                            <p class="font-bold text-on-surface">{{ $detail->durasi_minggu }} minggu</p>
                        </div>
                        <div class="bg-surface-container-low rounded-xl p-4">
                            <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Status Rincian</p>
                            <p class="font-bold text-on-surface">{{ ucfirst(str_replace('_', ' ', $detail->status ?? '-')) }}</p>
                        </div>
                    </div>

                    @if(!empty($detail->cost_breakdown))
                        <div class="mb-6">
                            <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-3">Cost Breakdown</p>
                            <div class="divide-y divide-surface-container rounded-xl border border-surface-container overflow-hidden">
                                @foreach($detail->cost_breakdown as $item)
                                    <div class="flex items-center justify-between gap-4 bg-white px-4 py-3 text-sm">
                                        <span class="font-semibold text-on-surface">{{ $item['nama'] ?? '-' }}</span>
                                        <span class="font-bold text-secondary">Rp {{ number_format($item['nominal'] ?? 0, 0, ',', '.') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($detail->catatan_teknis)
                        <div>
                            <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-2">Catatan Teknis</p>
                            <p class="text-sm text-on-surface-variant leading-relaxed bg-surface-container-low rounded-xl p-4">{{ $detail->catatan_teknis }}</p>
                        </div>
                    @endif
                @else
                    <div class="rounded-2xl bg-surface-container-low p-8 text-center">
                        <span class="material-symbols-outlined text-4xl text-on-surface-variant/40 mb-2">pending_actions</span>
                        <p class="font-bold text-on-surface">Rincian vendor belum tersedia</p>
                        <p class="text-sm text-on-surface-variant mt-1">Vendor belum mengirim rincian teknis proyek.</p>
                    </div>
                @endif
            </section>

            @php
                $submittedProgressUpdates = $proyek->penugasan
                    ->flatMap(fn ($penugasan) => $penugasan->submittedProgressUpdates)
                    ->sortByDesc('submitted_at');
            @endphp

            <section class="bg-surface-container-lowest rounded-2xl p-8 shadow-sm border border-surface-container">
                <h3 class="text-lg font-bold text-on-surface mb-6">Monitoring Progress Vendor</h3>

                @if($submittedProgressUpdates->isEmpty())
                    <div class="rounded-2xl bg-surface-container-low p-8 text-center">
                        <span class="material-symbols-outlined text-4xl text-on-surface-variant/40 mb-2">update</span>
                        <p class="font-bold text-on-surface">Belum ada update progress</p>
                        <p class="text-sm text-on-surface-variant mt-1">Vendor belum mengirim update progress untuk proyek ini.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($submittedProgressUpdates as $update)
                            <article class="rounded-xl border border-surface-container bg-white p-5">
                                <div class="flex items-start justify-between gap-4 mb-3">
                                    <div>
                                        <p class="text-2xl font-extrabold text-secondary">{{ $update->persentase }}%</p>
                                        <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">
                                            {{ $update->submitted_at?->format('d M Y H:i') ?? $update->created_at?->format('d M Y H:i') }}
                                        </p>
                                    </div>
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $update->status_progress === 'selesai' ? 'bg-tertiary-container text-on-tertiary-fixed' : 'bg-secondary-container text-on-secondary-fixed' }}">
                                        {{ $update->status_progress === 'selesai' ? 'Selesai' : 'Berjalan' }}
                                    </span>
                                </div>

                                <p class="text-sm text-on-surface-variant leading-relaxed whitespace-pre-line">{{ $update->deskripsi }}</p>

                                @if(!empty($update->foto_paths))
                                    <div class="grid grid-cols-2 gap-3 mt-4">
                                        @foreach($update->foto_paths as $path)
                                            <img src="{{ asset('storage/' . $path) }}" alt="Foto progress" class="h-32 w-full rounded-lg object-cover border border-surface-container">
                                        @endforeach
                                    </div>
                                @endif
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>

        <aside class="col-span-12 lg:col-span-5 space-y-8">
            <section class="bg-surface-container-lowest rounded-2xl p-8 shadow-sm border border-surface-container">
                <h3 class="text-lg font-bold text-on-surface mb-6">Penyedia Terpilih</h3>
                @if($proyek->penyedia)
                    <div class="space-y-3 text-sm">
                        <p class="text-xl font-bold text-on-surface">{{ $proyek->penyedia->nama }}</p>
                        <p class="text-on-surface-variant">{{ ucfirst(str_replace('_', ' ', $proyek->penyedia->spesialisasi ?? '-')) }}</p>
                        @if($proyek->penyedia->email)
                            <p class="text-on-surface-variant">{{ $proyek->penyedia->email }}</p>
                        @endif
                        @if($proyek->penyedia->telepon)
                            <p class="text-on-surface-variant">{{ $proyek->penyedia->telepon }}</p>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-on-surface-variant">Belum ada penyedia dipilih.</p>
                @endif
            </section>
        </aside>
    </div>
</div>
@endsection
