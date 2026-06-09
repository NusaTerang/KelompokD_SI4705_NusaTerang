@extends('layouts.app')

@section('content')

    {{-- Hero --}}
    <section class="bg-secondary relative overflow-hidden py-24 px-8 flex flex-col items-center text-center">
        <div class="absolute inset-0 opacity-10 pointer-events-none">
            <div class="absolute top-0 right-0 w-96 h-96 bg-primary-container rounded-full blur-[120px]"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-tertiary-container rounded-full blur-[100px]"></div>
        </div>

        <div class="relative z-10 max-w-4xl w-full">
            <h1 class="font-headline font-extrabold text-5xl md:text-6xl text-white mb-6 leading-tight">
                Proyek Energi Aktif
            </h1>
            <p class="font-body text-lg text-white/80 max-w-2xl mx-auto mb-12">
                Bersama wujudkan kemandirian energi di pelosok Nusantara. Pilih proyek berkelanjutan dan pantau dampak donasi Anda secara transparan.
            </p>

            <form action="{{ url('/') }}" method="GET" class="max-w-2xl mx-auto w-full">
                <div class="bg-white rounded-xl p-2 flex items-center shadow-2xl">
                    <span class="material-symbols-outlined text-outline-variant px-4">search</span>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama desa atau wilayah..."
                        class="w-full border-none focus:ring-0 text-on-surface py-3 font-body bg-transparent outline-none"
                    />
                    <button type="submit" class="bg-secondary text-white px-8 py-3 rounded-lg font-headline font-bold ml-2 hover:opacity-90 transition-all">
                        Cari
                    </button>
                </div>
            </form>
        </div>
    </section>

    {{-- Filter Bar --}}
    <div class="sticky top-20 z-40 bg-surface/90 backdrop-blur-md py-6 shadow-sm">
        <div class="max-w-7xl mx-auto px-8 flex flex-wrap items-center justify-between gap-6">

            <div class="flex items-center gap-3">
                <a href="{{ url()->current() }}{{ request()->filled('search') || request()->filled('wilayah') ? '?' . http_build_query(request()->only(['search', 'wilayah'])) : '' }}"
                   class="{{ !request('status') ? 'px-6 py-2 rounded-full bg-secondary text-white font-bold shadow-md' : 'px-6 py-2 rounded-full border border-outline-variant text-on-surface-variant font-medium hover:bg-surface-container transition-all' }}">
                    Semua
                </a>
                <a href="{{ url()->current() }}?{{ http_build_query(array_merge(request()->only(['search', 'wilayah']), ['status' => 'berlangsung'])) }}"
                   class="{{ request('status') == 'berlangsung' ? 'px-6 py-2 rounded-full bg-secondary text-white font-bold shadow-md' : 'px-6 py-2 rounded-full border border-outline-variant text-on-surface-variant font-medium hover:bg-surface-container transition-all' }}">
                    Berlangsung
                </a>
                <a href="{{ url()->current() }}?{{ http_build_query(array_merge(request()->only(['search', 'wilayah']), ['status' => 'terpenuhi'])) }}"
                   class="{{ request('status') == 'terpenuhi' ? 'px-6 py-2 rounded-full bg-secondary text-white font-bold shadow-md' : 'px-6 py-2 rounded-full border border-outline-variant text-on-surface-variant font-medium hover:bg-surface-container transition-all' }}">
                    Terpenuhi
                </a>
                <a href="{{ url()->current() }}?{{ http_build_query(array_merge(request()->only(['search', 'wilayah']), ['status' => 'selesai'])) }}"
                   class="{{ request('status') == 'selesai' ? 'px-6 py-2 rounded-full bg-secondary text-white font-bold shadow-md' : 'px-6 py-2 rounded-full border border-outline-variant text-on-surface-variant font-medium hover:bg-surface-container transition-all' }}">
                    Selesai
                </a>
            </div>

            <form action="{{ url()->current() }}" method="GET" class="flex items-center gap-4">
                @if(request()->filled('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                @if(request()->filled('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <span class="text-on-surface-variant text-sm font-medium">Filter wilayah:</span>
                <select name="wilayah" onchange="this.form.submit()" class="bg-surface-container-highest border-none rounded-lg font-body text-sm px-4 py-2 focus:ring-2 focus:ring-secondary cursor-pointer text-on-surface outline-none">
                    <option value="">Seluruh Indonesia</option>
                    @foreach($provinceOptions as $province)
                        <option value="{{ $province }}" @selected(request('wilayah') === $province)>{{ $province }}</option>
                    @endforeach
                </select>
            </form>

        </div>
    </div>

    {{-- Project Grid --}}
    <section class="max-w-7xl mx-auto px-8 py-12">
        @if($projects->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($projects as $project)
                    @php
                        $rawProgress   = $project->target_dana > 0 ? round(($project->dana_terkumpul / $project->target_dana) * 100) : 0;
                        $progress      = in_array($project->status, ['eksekusi', 'selesai']) ? max($rawProgress, 100) : $rawProgress;
                        $daysLeft      = $project->estimasi_selesai ? max(0, (int) ceil(now()->diffInDays($project->estimasi_selesai, false))) : 0;
                        $firstFoto = $project->fotos->first();
                        $imageUrl  = $firstFoto
                            ? (str_starts_with($firstFoto->path, 'http') ? $firstFoto->path : asset('storage/' . $firstFoto->path))
                            : asset('images/default-project.svg');
                        $locationBadge = $project->desa
                            ? strtoupper($project->desa->provinsi . ', ' . $project->desa->nama_desa)
                            : 'LOKASI TIDAK DIKETAHUI';
                    @endphp

                    <div class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all flex flex-col group overflow-hidden border border-transparent hover:border-outline-variant/10">

                        {{-- Image & Badge --}}
                        <div class="relative h-[200px] overflow-hidden">
                            <img
                                src="{{ $imageUrl }}"
                                alt="{{ $project->judul }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            />
                            <div class="absolute top-4 left-4 bg-white/90 backdrop-blur px-3 py-1 rounded-full flex items-center gap-1.5 shadow-sm">
                                <span class="material-symbols-outlined text-sm text-secondary">location_on</span>
                                <span class="text-[11px] font-bold text-secondary uppercase tracking-wider">{{ $locationBadge }}</span>
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="p-6 flex flex-col flex-grow">
                            <h3 class="font-headline font-bold text-xl text-on-surface mb-2">{{ $project->judul }}</h3>
                            <p class="text-on-surface-variant text-sm line-clamp-2 mb-6">{{ $project->deskripsi }}</p>

                            <div class="mt-auto">
                                {{-- Progress --}}
                                <div class="flex justify-between items-end mb-2">
                                    <span class="text-2xl font-extrabold text-secondary">{{ $progress }}%</span>
                                    <span class="text-xs text-on-surface-variant font-medium">Target: Rp {{ number_format($project->target_dana, 0, ',', '.') }}</span>
                                </div>
                                <div class="w-full h-2.5 bg-surface-container rounded-full overflow-hidden mb-6">
                                    <div class="h-full solar-gradient rounded-full" style="width: {{ min($progress, 100) }}%"></div>
                                </div>

                                {{-- Stats --}}
                                <div class="flex justify-between py-4 border-t border-surface-container-low mb-6">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] uppercase text-outline tracking-widest font-bold">Terkumpul</span>
                                        <span class="text-sm font-bold text-on-surface">Rp {{ number_format($project->dana_terkumpul, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex flex-col text-right">
                                        <span class="text-[10px] uppercase text-outline tracking-widest font-bold">Sisa Hari</span>
                                        <span class="text-sm font-bold text-on-surface">{{ $daysLeft }} Hari</span>
                                    </div>
                                </div>

                                {{-- Action --}}
                                <a href="{{ route('proyek.show', $project->id_proyek ?? $project->id) }}"
                                   class="w-full py-3 border-2 border-secondary text-secondary rounded-lg font-headline font-bold hover:bg-secondary hover:text-white transition-all flex items-center justify-center gap-2">
                                    Lihat Detail
                                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-16 flex justify-center">
                {{ $projects->links() }}
            </div>

        @else
            <div class="py-20 text-center flex flex-col items-center justify-center bg-white rounded-2xl border border-dashed border-outline-variant">
                <span class="material-symbols-outlined text-[64px] text-outline-variant mb-4">inbox</span>
                <h3 class="text-xl font-headline font-bold text-on-surface mb-2">Tidak ada proyek ditemukan</h3>
                <p class="text-on-surface-variant max-w-md">Belum ada proyek energi yang sesuai dengan pencarian atau filter Anda saat ini.</p>
            </div>
        @endif
    </section>

    {{-- CTA --}}
    <section class="max-w-7xl mx-auto px-8 mb-24">
        <div class="bg-surface-container-low rounded-3xl p-12 flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="max-w-lg">
                <h2 class="font-headline font-extrabold text-3xl text-secondary mb-4">Ingin proyek desa Anda dibantu?</h2>
                <p class="text-on-surface-variant">Daftarkan desa atau komunitas Anda sebagai penyedia energi dan dapatkan dukungan pendanaan dari donatur di seluruh dunia.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="{{ route('desa.input') }}" class="px-8 py-4 bg-secondary text-white rounded-xl font-headline font-bold shadow-lg hover:opacity-90 transition-all text-center">
                    Ajukan Proyek
                </a>
                <a href="#" class="px-8 py-4 bg-white text-secondary border-2 border-secondary rounded-xl font-headline font-bold hover:bg-surface-container transition-all text-center">
                    Pelajari Lebih Lanjut
                </a>
            </div>
        </div>
    </section>

@endsection
