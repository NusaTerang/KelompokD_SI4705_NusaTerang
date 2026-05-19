@extends('layouts.app')

@section('content')

@php
    $progress  = $proyek->target_dana > 0 ? round(($proyek->dana_terkumpul / $proyek->target_dana) * 100) : 0;
    $daysLeft  = $proyek->estimasi_selesai ? max(0, now()->diffInDays($proyek->estimasi_selesai, false)) : 0;
    $firstFoto = $proyek->fotos->first();
    $imageUrl  = $firstFoto
        ? (str_starts_with($firstFoto->path, 'http') ? $firstFoto->path : asset('storage/' . $firstFoto->path))
        : asset('images/default-project.svg');
    $location  = $proyek->desa
        ? $proyek->desa->nama_desa . ', ' . $proyek->desa->provinsi
        : 'Lokasi tidak diketahui';

    $statusMap = [
        'draft'                          => ['text' => 'DRAFT',              'bg' => 'bg-surface-container',    'color' => 'text-on-surface-variant'],
        'menunggu_konfirmasi_penyedia'   => ['text' => 'MENUNGGU PENYEDIA',  'bg' => 'bg-yellow-100',           'color' => 'text-yellow-800'],
        'diterima_penyedia'              => ['text' => 'DITERIMA PENYEDIA',  'bg' => 'bg-secondary-fixed',      'color' => 'text-on-secondary-fixed'],
        'menunggu_review_admin'          => ['text' => 'MENUNGGU REVIEW',    'bg' => 'bg-yellow-100',           'color' => 'text-yellow-800'],
        'aktif_funding'                  => ['text' => 'SEDANG BERJALAN',    'bg' => 'bg-primary-container',    'color' => 'text-on-primary-fixed'],
        'eksekusi'                       => ['text' => 'DALAM EKSEKUSI',     'bg' => 'bg-secondary-container',  'color' => 'text-on-secondary-fixed'],
        'selesai'                        => ['text' => 'SELESAI',            'bg' => 'bg-tertiary-container',   'color' => 'text-on-tertiary-fixed'],
        'ditolak'                        => ['text' => 'DITOLAK',            'bg' => 'bg-error-container',      'color' => 'text-on-error-container'],
    ];
    $status = $statusMap[$proyek->status] ?? ['text' => strtoupper($proyek->status), 'bg' => 'bg-surface-container', 'color' => 'text-on-surface-variant'];
@endphp

<div class="w-full max-w-[1216px] mx-auto px-4 py-12 flex flex-col lg:flex-row gap-12 items-start">

    {{-- Left Column --}}
    <div class="w-full lg:flex-1 flex flex-col gap-8">

        {{-- Hero Image --}}
        <div class="w-full relative rounded-xl overflow-hidden shadow-md h-[300px]">
            <img src="{{ $imageUrl }}" alt="{{ $proyek->judul }}" class="w-full h-full object-cover" />
            <div class="absolute top-6 left-6 {{ $status['bg'] }} px-4 py-1.5 rounded-full shadow-sm">
                <span class="{{ $status['color'] }} text-xs font-bold font-headline tracking-wider uppercase">
                    {{ $status['text'] }}
                </span>
            </div>
        </div>

        {{-- Title & Location --}}
        <div class="flex flex-col gap-3">
            <h1 class="text-on-surface text-3xl md:text-4xl font-headline font-semibold leading-tight">
                {{ $proyek->judul }}
            </h1>
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px] text-on-surface-variant">location_on</span>
                <span class="text-on-surface-variant text-base font-medium">{{ $location }}</span>
            </div>
        </div>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 w-full">
            <div class="bg-surface-container-low p-4 rounded-xl border-l-4 border-primary flex flex-col gap-1">
                <p class="text-on-surface-variant text-xs font-bold uppercase tracking-wide">TERKUMPUL</p>
                <p class="text-on-surface text-lg font-bold">Rp {{ number_format($proyek->dana_terkumpul / 1000000, 1) }}jt</p>
            </div>
            <div class="bg-surface-container-low p-4 rounded-xl border-l-4 border-outline-variant flex flex-col gap-1">
                <p class="text-on-surface-variant text-xs font-bold uppercase tracking-wide">TARGET</p>
                <p class="text-on-surface text-lg font-bold">Rp {{ number_format($proyek->target_dana / 1000000, 1) }}jt</p>
            </div>
            <div class="bg-surface-container-low p-4 rounded-xl border-l-4 border-secondary-container flex flex-col gap-1">
                <p class="text-on-surface-variant text-xs font-bold uppercase tracking-wide">DONATUR</p>
                <p class="text-on-surface text-lg font-bold">0</p>
            </div>
            <div class="bg-surface-container-low p-4 rounded-xl border-l-4 border-tertiary-container flex flex-col gap-1">
                <p class="text-on-surface-variant text-xs font-bold uppercase tracking-wide">SISA HARI</p>
                <p class="text-on-surface text-lg font-bold">{{ $daysLeft }} Hari</p>
            </div>
        </div>

        {{-- Progress Bar --}}
        <div class="flex flex-col gap-2 w-full">
            <div class="flex justify-between items-end">
                <p class="text-on-surface-variant text-sm font-bold">Kemajuan Pendanaan</p>
                <p class="text-primary text-2xl font-bold">{{ $progress }}%</p>
            </div>
            <div class="w-full h-4 bg-surface-container rounded-full overflow-hidden">
                <div class="h-full solar-gradient rounded-full" style="width: {{ min($progress, 100) }}%"></div>
            </div>
        </div>

        {{-- About --}}
        <div class="flex flex-col gap-4 w-full">
            <h2 class="text-secondary text-2xl font-headline font-semibold">Tentang Proyek</h2>
            @if($proyek->deskripsi)
                <div class="text-on-surface-variant text-lg leading-relaxed whitespace-pre-line">{{ $proyek->deskripsi }}</div>
            @else
                <p class="text-on-surface-variant italic">Belum ada deskripsi proyek.</p>
            @endif
        </div>

        {{-- Photo Gallery --}}
        @if($proyek->fotos->count() > 1)
            <div class="flex flex-col gap-4 w-full">
                <h2 class="text-secondary text-2xl font-headline font-semibold">Galeri</h2>
                <div class="flex gap-4 overflow-x-auto pb-2 hide-scrollbar">
                    @foreach($proyek->fotos as $foto)
                        <img
                            src="{{ str_starts_with($foto->path, 'http') ? $foto->path : asset('storage/' . $foto->path) }}"
                            alt="Foto proyek"
                            class="h-40 w-64 rounded-xl object-cover shrink-0 border border-surface-container"
                        />
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Project Info Details --}}
        <div class="flex flex-col gap-4 w-full">
            <h2 class="text-secondary text-2xl font-headline font-semibold">Informasi Proyek</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-surface-container-low p-4 rounded-xl flex flex-col gap-1">
                    <p class="text-on-surface-variant text-xs font-bold uppercase tracking-wide">JENIS ENERGI</p>
                    <p class="text-on-surface font-semibold capitalize">{{ str_replace('_', ' ', $proyek->jenis_energi ?? '-') }}</p>
                </div>
                <div class="bg-surface-container-low p-4 rounded-xl flex flex-col gap-1">
                    <p class="text-on-surface-variant text-xs font-bold uppercase tracking-wide">PENYEDIA</p>
                    <p class="text-on-surface font-semibold">{{ $proyek->penyedia ? $proyek->penyedia->nama : 'Belum ditentukan' }}</p>
                </div>
                @if($proyek->estimasi_mulai)
                    <div class="bg-surface-container-low p-4 rounded-xl flex flex-col gap-1">
                        <p class="text-on-surface-variant text-xs font-bold uppercase tracking-wide">ESTIMASI MULAI</p>
                        <p class="text-on-surface font-semibold">{{ $proyek->estimasi_mulai->format('d M Y') }}</p>
                    </div>
                @endif
                @if($proyek->estimasi_selesai)
                    <div class="bg-surface-container-low p-4 rounded-xl flex flex-col gap-1">
                        <p class="text-on-surface-variant text-xs font-bold uppercase tracking-wide">ESTIMASI SELESAI</p>
                        <p class="text-on-surface font-semibold">{{ $proyek->estimasi_selesai->format('d M Y') }}</p>
                    </div>
                @endif
            </div>
        </div>

        @php
            $submittedProgressUpdates = $proyek->penugasan
                ->flatMap(fn ($penugasan) => $penugasan->submittedProgressUpdates)
                ->sortByDesc('submitted_at');
        @endphp

        <div class="flex flex-col gap-6 w-full py-6">
            <h2 class="text-secondary text-2xl font-headline font-semibold">Update Progress</h2>

            @if($submittedProgressUpdates->isEmpty())
                <div class="flex flex-col items-center justify-center py-12 bg-surface-container-low rounded-xl">
                    <span class="material-symbols-outlined text-[48px] text-outline-variant mb-3">update</span>
                    <p class="text-on-surface-variant text-sm">Belum ada update progress untuk proyek ini.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($submittedProgressUpdates as $update)
                        <article class="bg-surface-container-low rounded-xl p-5 border border-surface-container">
                            <div class="flex items-start justify-between gap-4 mb-3">
                                <div>
                                    <p class="text-primary text-2xl font-bold">{{ $update->persentase }}%</p>
                                    <p class="text-xs text-on-surface-variant font-bold uppercase tracking-wide">
                                        {{ $update->submitted_at?->format('d M Y H:i') ?? $update->created_at?->format('d M Y H:i') }}
                                    </p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $update->status_progress === 'selesai' ? 'bg-tertiary-container text-on-tertiary-fixed' : 'bg-secondary-container text-on-secondary-fixed' }}">
                                    {{ $update->status_progress === 'selesai' ? 'Selesai' : 'Berjalan' }}
                                </span>
                            </div>

                            <p class="text-on-surface-variant leading-relaxed whitespace-pre-line">{{ $update->deskripsi }}</p>

                            @if(!empty($update->foto_paths))
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mt-4">
                                    @foreach($update->foto_paths as $path)
                                        <img src="{{ asset('storage/' . $path) }}" alt="Foto progress" class="h-32 w-full object-cover rounded-lg border border-surface-container">
                                    @endforeach
                                </div>
                            @endif
                        </article>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    {{-- Right Column - Donation Card --}}
    <div class="w-full lg:w-[420px] flex flex-col sticky top-24 shrink-0">
        <div class="bg-white rounded-2xl p-8 border border-surface-container shadow-xl flex flex-col gap-6 w-full">

            <h2 class="text-secondary text-2xl font-headline font-semibold">Dukung Proyek Ini</h2>

            <div class="flex flex-col gap-2">
                <div class="flex justify-between items-center">
                    <p class="text-on-surface-variant text-xs font-bold uppercase">TERKUMPUL</p>
                    <p class="text-tertiary text-2xl font-bold">Rp {{ number_format($proyek->dana_terkumpul, 0, ',', '.') }}</p>
                </div>
                <div class="w-full h-2 bg-surface-container rounded-full overflow-hidden my-1">
                    <div class="h-full solar-gradient rounded-full" style="width: {{ min($progress, 100) }}%"></div>
                </div>
                <div class="flex gap-1 text-xs">
                    <span class="text-on-surface-variant">Dari target</span>
                    <span class="text-on-surface font-bold">Rp {{ number_format($proyek->target_dana, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 w-full">
                <button class="py-3 rounded-xl border border-outline-variant text-on-surface font-bold hover:bg-surface-container-low transition-colors">Rp 50k</button>
                <button class="py-3 rounded-xl border border-outline-variant text-on-surface font-bold hover:bg-surface-container-low transition-colors">Rp 100k</button>
                <button class="py-3 rounded-xl border border-outline-variant text-on-surface font-bold hover:bg-surface-container-low transition-colors">Rp 250k</button>
                <button class="py-3 rounded-xl border border-outline-variant text-on-surface font-bold hover:bg-surface-container-low transition-colors">Rp 500k</button>
            </div>

            <div class="flex flex-col gap-2 w-full">
                <label class="text-on-surface-variant text-sm font-bold">Nominal Donasi Lainnya</label>
                <div class="relative w-full">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant font-bold text-sm">Rp</span>
                    <input
                        type="text"
                        placeholder="0"
                        class="w-full bg-surface-container-low rounded-xl py-4 pl-12 pr-4 outline-none text-right font-bold text-on-surface focus:ring-2 focus:ring-secondary border-none"
                    />
                </div>
            </div>

            <a href="{{ route('login') }}" class="w-full bg-primary-container text-on-primary-fixed font-headline font-extrabold text-lg py-4 rounded-xl shadow-md hover:opacity-90 transition-all mt-2 text-center block">
                Donasi Sekarang
            </a>

            <div class="flex items-center gap-6 pt-2 border-t border-surface-container">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[16px] text-tertiary">verified_user</span>
                    <span class="text-[10px] font-bold text-on-surface-variant uppercase">TRANSAKSI AMAN</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[16px] text-tertiary">eco</span>
                    <span class="text-[10px] font-bold text-on-surface-variant uppercase">100% BERKELANJUTAN</span>
                </div>
            </div>

        </div>
    </div>

</div>

@endsection
