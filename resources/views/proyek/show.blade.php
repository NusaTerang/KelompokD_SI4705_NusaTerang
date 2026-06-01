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
        'aktif_funding'                  => ['text' => 'Menunggu Dana',      'bg' => 'bg-primary-container',    'color' => 'text-on-primary-fixed'],
        'eksekusi'                       => ['text' => 'Sedang Berjalan',    'bg' => 'bg-secondary-container',  'color' => 'text-on-secondary-fixed'],
        'selesai'                        => ['text' => 'Selesai',            'bg' => 'bg-tertiary-container',   'color' => 'text-on-tertiary-fixed'],
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
                ->sortByDesc('submitted_at')
                ->values();
            $latestProgressUpdate = $submittedProgressUpdates->first();
            $monitoringProgress = $latestProgressUpdate ? (int) $latestProgressUpdate->persentase : 0;
            $monitoringProgressWidth = max(0, min($monitoringProgress, 100));
        @endphp

        <div class="flex flex-col gap-6 w-full py-6">
            <div class="bg-surface-container-low rounded-xl p-5 border border-surface-container flex flex-col gap-3">
                <div class="flex justify-between items-end gap-4">
                    <div>
                        <p class="text-on-surface-variant text-sm font-bold">Progress Pengerjaan</p>
                        @if($latestProgressUpdate)
                            <p class="text-xs text-on-surface-variant mt-1">Update terakhir {{ $latestProgressUpdate->submitted_at?->format('d M Y H:i') ?? $latestProgressUpdate->created_at?->format('d M Y H:i') }}</p>
                        @else
                            <p class="text-xs text-on-surface-variant mt-1">Belum ada update progres dari vendor.</p>
                        @endif
                    </div>
                    <p class="text-secondary text-2xl font-bold">{{ $monitoringProgress }}%</p>
                </div>
                <div class="w-full h-3 bg-surface-container rounded-full overflow-hidden">
                    <div class="h-full bg-secondary rounded-full" style="width: {{ $monitoringProgressWidth }}%"></div>
                </div>
            </div>

            <h2 class="text-secondary text-2xl font-headline font-semibold">Timeline & Progress Updates</h2>

            @if($submittedProgressUpdates->isEmpty())
                <div class="flex flex-col items-center justify-center py-12 bg-surface-container-low rounded-xl">
                    <span class="material-symbols-outlined text-[48px] text-outline-variant mb-3">update</span>
                    <p class="text-on-surface-variant text-sm">Vendor belum mengunggah update progres</p>
                </div>
            @else
                <div class="relative flex flex-col gap-8">
                    @foreach($submittedProgressUpdates as $update)
                        @php
                            $submittedAt = $update->submitted_at ?? $update->created_at;
                            $photoPaths = is_array($update->foto_paths) ? $update->foto_paths : [];
                            $isDone = $update->status_progress === 'selesai';
                        @endphp
                        <article class="grid grid-cols-[72px_24px_1fr] gap-4 items-start">
                            <div class="text-right pt-1">
                                <p class="text-sm font-extrabold text-on-surface-variant">{{ $submittedAt?->format('d M') }}</p>
                                <p class="text-[10px] font-bold text-on-surface-variant/60 uppercase tracking-widest">{{ $submittedAt?->format('Y') }}</p>
                            </div>

                            <div class="relative flex justify-center h-full min-h-[120px]">
                                @if(!$loop->last)
                                    <span class="absolute top-6 bottom-[-32px] w-0.5 bg-surface-container"></span>
                                @endif
                                <span class="relative z-10 mt-2 w-4 h-4 rounded-full {{ $isDone ? 'bg-tertiary' : 'bg-primary-container' }} ring-4 {{ $isDone ? 'ring-tertiary/10' : 'ring-primary-container/30' }}"></span>
                            </div>

                            <div class="bg-surface-container-low rounded-2xl p-5 border {{ $loop->first ? 'border-primary-container shadow-sm' : 'border-surface-container' }}">
                                <div class="flex items-start justify-between gap-4 mb-3">
                                    <div>
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold {{ $isDone ? 'bg-tertiary-container text-on-tertiary-fixed' : 'bg-secondary-container text-on-secondary-fixed' }}">
                                            {{ $isDone ? 'Selesai' : 'Sedang Berjalan' }}
                                        </span>
                                        <p class="text-xs text-on-surface-variant font-bold uppercase tracking-wide mt-2">
                                            {{ $submittedAt?->format('d M Y H:i') }}
                                        </p>
                                    </div>
                                    <p class="text-primary text-2xl font-bold">{{ $update->persentase }}%</p>
                                </div>

                                <p class="text-on-surface-variant leading-relaxed whitespace-pre-line">{{ $update->deskripsi }}</p>

                                @if(!empty($photoPaths))
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mt-4">
                                        @foreach($photoPaths as $path)
                                            <img src="{{ str_starts_with($path, 'http') ? $path : asset('storage/' . $path) }}" alt="Foto progress" class="h-32 w-full object-cover rounded-lg border border-surface-container">
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif

            @if($proyek->submittedFinalReport)
                @php
                    $finalReport = $proyek->submittedFinalReport;
                    $finalReportPhotos = is_array($finalReport->foto_paths) ? $finalReport->foto_paths : [];
                @endphp
                <div class="bg-tertiary-container/30 border border-tertiary/20 rounded-xl p-5 flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-tertiary">task_alt</span>
                        <h3 class="text-secondary text-xl font-headline font-semibold">Laporan Akhir</h3>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-white/70 rounded-lg p-4 border border-tertiary/10">
                            <p class="text-on-surface-variant text-xs font-bold uppercase tracking-wide mb-1">Kapasitas Terpasang</p>
                            <p class="text-primary text-2xl font-bold">{{ rtrim(rtrim(number_format((float) $finalReport->kapasitas_terpasang, 2, '.', ''), '0'), '.') }} {{ $finalReport->satuan_kapasitas }}</p>
                        </div>
                        <div class="bg-white/70 rounded-lg p-4 border border-tertiary/10">
                            <p class="text-on-surface-variant text-xs font-bold uppercase tracking-wide mb-1">Tanggal Submit</p>
                            <p class="text-on-surface font-semibold">{{ $finalReport->submitted_at?->format('d M Y H:i') ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="bg-white/70 rounded-lg p-4 border border-tertiary/10">
                        <p class="text-on-surface-variant text-xs font-bold uppercase tracking-wide mb-2">Deskripsi Hasil Pekerjaan</p>
                        <p class="text-on-surface-variant whitespace-pre-line leading-relaxed">{{ $finalReport->deskripsi }}</p>
                    </div>
                    @if($finalReport->catatan)
                        <div class="bg-white/70 rounded-lg p-4 border border-tertiary/10">
                            <p class="text-on-surface-variant text-xs font-bold uppercase tracking-wide mb-2">Catatan Tambahan</p>
                            <p class="text-on-surface-variant whitespace-pre-line leading-relaxed">{{ $finalReport->catatan }}</p>
                        </div>
                    @endif
                    @if(!empty($finalReportPhotos))
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach($finalReportPhotos as $path)
                                <img src="{{ str_starts_with($path, 'http') ? $path : asset('storage/' . $path) }}" alt="Foto laporan akhir" class="h-32 w-full object-cover rounded-lg border border-tertiary/10">
                            @endforeach
                        </div>
                    @endif
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
