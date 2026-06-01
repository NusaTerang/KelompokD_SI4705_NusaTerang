@extends('layouts.admin')

@section('title', 'Desa Prioritas')

@section('breadcrumbs')
    <span>Dashboard</span>
    <span class="mx-1 text-slate-400">/</span>
    <span>Data Desa</span>
    <span class="mx-1 text-slate-400">/</span>
    <span class="font-medium text-slate-700">Desa Prioritas</span>
@endsection

@section('page_heading', 'Desa Prioritas')

@section('content')
    <div class="mx-auto max-w-7xl space-y-6">
        
        {{-- Banner Info Sistem Rekomendasi --}}
        <div class="flex flex-col gap-3 rounded-2xl border border-amber-200 p-4 sm:flex-row sm:items-start sm:gap-4" style="background-color: rgba(255,251,235,0.9);">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-200 text-amber-900" aria-hidden="true">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2a7 7 0 00-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6a2.5 2.5 0 010 5.5z"/></svg>
            </span>
            <div>
                <h2 class="text-sm font-semibold text-amber-950">Sistem Rekomendasi</h2>
                <p class="mt-1 text-sm leading-relaxed text-amber-950/80">
                    Algoritme menganalisis 1.200+ desa berdasarkan rasio elektrifikasi, potensi radiasi surya, populasi produktif, dan kesiapan infrastruktur untuk menghasilkan skor prioritas.
                </p>
            </div>
        </div>

        {{-- Search dan Filter Bar --}}
        <form class="flex flex-col gap-3 lg:flex-row lg:flex-wrap lg:items-end" action="{{ route('desa.kelola') }}" method="get">
            <div class="min-w-0 flex-1">
                <label class="sr-only" for="q">Cari</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                    </span>
                    <input
                        type="search"
                        name="q"
                        id="q"
                        value="{{ request('q') }}"
                        placeholder="Cari nama desa..."
                        class="w-full rounded-lg border border-slate-300 py-2.5 pl-10 pr-3 text-sm shadow-sm focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-400/20"
                    />
                </div>
            </div>
            
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3 lg:w-auto lg:grid-cols-none lg:flex lg:gap-3">
                <select name="provinsi" class="rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-400/20 lg:min-w-[140px]">
                    <option value="">Provinsi</option>
                    @foreach($availableProvinces ?? [] as $prov)
                        <option value="{{ $prov }}" {{ request('provinsi') == $prov ? 'selected' : '' }}>{{ $prov }}</option>
                    @endforeach
                </select>
                <select name="status_listrik" class="rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-400/20 lg:min-w-[160px]">
                    <option value="">Status Listrik</option>
                    <option value="off-grid" {{ request('status_listrik') == 'off-grid' ? 'selected' : '' }}>Off-grid</option>
                    <option value="terbatas" {{ request('status_listrik') == 'terbatas' ? 'selected' : '' }}>Terbatas</option>
                    <option value="teraliri" {{ request('status_listrik') == 'teraliri' ? 'selected' : '' }}>Teraliri</option>
                </select>
                <select name="tipe_energi" class="rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-400/20 lg:min-w-[140px]">
                    <option value="">Tipe Energi</option>
                    <option value="panel_surya" {{ request('tipe_energi') == 'panel_surya' ? 'selected' : '' }}>Panel Surya</option>
                    <option value="mikro_hidro" {{ request('tipe_energi') == 'mikro_hidro' ? 'selected' : '' }}>Mikro Hidro</option>
                    <option value="biogas" {{ request('tipe_energi') == 'biogas' ? 'selected' : '' }}>Biogas</option>
                    <option value="hybrid_solar_baterai" {{ request('tipe_energi') == 'hybrid_solar_baterai' ? 'selected' : '' }}>Hybrid Solar + Baterai</option>
                </select>
            </div>
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-opacity hover:opacity-90 lg:shrink-0" style="background-color: #0f4c81;">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" /></svg>
                Sort: Highest Priority
            </button>
            @if(request()->anyFilled(['q', 'provinsi', 'status_listrik', 'tipe_energi']))
                <a href="{{ route('desa.kelola') }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 lg:shrink-0">
                    Reset
                </a>
            @endif
        </form>

        {{-- Grid Card Daftar Desa --}}
        <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
            @forelse (($desaPrioritas ?? []) as $item)
                @php
                    $rank = $item['rank'];
                    $namaDesa = $item['nama_desa'];
                    $lokasi = $item['lokasi'];
                    $skor = (float) $item['skor_prioritas'];
                    $populasi = (int) $item['populasi'];
                    $statusListrik = $item['status_listrik'];
                    $yieldKwp = $item['yield_kwp'];
                    $gambar = $item['gambar'] ?? 'https://picsum.photos/seed/village' . $rank . '/640/360';
                    $solarOpt = $item['solar_optimized'] ?? false;
                    $urlDetail = $item['url_detail'] ?? '#';
                    $urlProyek = $item['url_proyek'] ?? '#';
                    $hasProject = $item['has_project'] ?? false;
                    
                    // Mewarnai status listrik (Off-grid = Merah, Terbatas = Orange)
                    $statusStyles = match (strtoupper($statusListrik)) {
                        'OFF-GRID', 'BELUM TERALIRI' => 'bg-red-100 text-red-800',
                        'TERBATAS', 'SEBAGIAN' => 'bg-orange-100 text-orange-800',
                        default => 'bg-emerald-100 text-emerald-800',
                    };
                    $scoreWidth = min(100, max(0, $skor));
                @endphp
                
                <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-shadow hover:shadow-md">
                    {{-- Bagian Gambar dan Badge Ranking --}}
                    <div class="relative aspect-[16/10] w-full overflow-hidden bg-slate-200">
                        <img src="{{ $gambar }}" alt="Foto {{ $namaDesa }}" class="h-full w-full object-cover" loading="lazy" />
                        <span class="absolute left-3 top-3 rounded-full px-2.5 py-1 text-xs font-bold shadow" style="background-color: #FDD835; color: #0f4c81;">RANK #{{ $rank }}</span>
                        
                        @if ($solarOpt)
                            <span class="absolute bottom-3 right-3 rounded-md bg-white/90 px-2 py-1 text-[10px] font-bold uppercase tracking-wide backdrop-blur" style="color: #0f4c81;">Solar Optimized</span>
                        @endif
                    </div>
                    
                    <div class="p-4">
                        <div class="mb-3 flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <h3 class="truncate text-lg font-bold" style="color: #0f4c81;">{{ $namaDesa }}</h3>
                                <p class="mt-0.5 flex items-center gap-1 text-xs text-slate-500">
                                    <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                                    {{ $lokasi }}
                                </p>
                            </div>
                            
                            {{-- Visualisasi Progress Skor --}}
                            <div class="shrink-0 text-right">
                                <p class="text-2xl font-bold leading-none" style="color: #0f4c81;">{{ number_format($skor, 1) }}</p>
                                <p class="mt-0.5 text-[10px] font-semibold uppercase tracking-wide text-slate-400">Priority Score</p>
                                <div class="mt-2 h-1.5 w-24 overflow-hidden rounded-full bg-slate-200">
                                    <div class="h-full rounded-full bg-emerald-500 transition-all" style="width: {{ $scoreWidth }}%"></div>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Ringkasan Parameter (Populasi, Status, Yield) --}}
                        <dl class="mb-4 grid grid-cols-3 gap-2 border-t border-slate-100 pt-3 text-center">
                            <div>
                                <dt class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Populasi</dt>
                                <dd class="text-sm font-semibold text-slate-800">{{ number_format($populasi) }}</dd>
                            </div>
                            <div>
                                <dt class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Status</dt>
                                <dd class="mt-0.5">
                                    <span class="inline-block rounded px-1.5 py-0.5 text-[10px] font-bold {{ $statusStyles }}">{{ $statusListrik }}</span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Yield</dt>
                                <dd class="text-sm font-semibold text-emerald-600">{{ $yieldKwp > 0 ? $yieldKwp : match($rank % 3) { 0 => '5.5', 1 => '5.2', 2 => '4.8', default => '5.0' } }} kWp</dd>
                            </div>
                        </dl>
                        
                        {{-- Tombol Aksi --}}
                        <div class="flex gap-2">
                            <a href="{{ $urlDetail }}" class="inline-flex flex-1 items-center justify-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Lihat Detail</a>
                            @if($hasProject)
                                <button type="button" disabled class="inline-flex flex-1 items-center justify-center rounded-lg border border-slate-200 bg-slate-100 px-3 py-2 text-sm font-bold text-slate-400 cursor-not-allowed">Proyek Dibuat</button>
                            @else
                                <a href="{{ $urlProyek }}" class="inline-flex flex-1 items-center justify-center rounded-lg px-3 py-2 text-sm font-bold shadow-sm transition-opacity hover:opacity-90" style="background-color: #FDD835; color: #0f4c81;">Buat Proyek</a>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                {{-- State Kosong --}}
                <div class="col-span-full flex flex-col items-center justify-center rounded-3xl border-2 border-dashed border-slate-200 bg-slate-50 py-16 text-center">
                    <svg class="mb-4 h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v16.5c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9zm3.75 11.625a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                    <h3 class="text-lg font-bold text-slate-700">Data Desa Tidak Ditemukan</h3>
                    <p class="mt-1 max-w-sm text-sm text-slate-500">Belum ada data desa yang cocok dengan kriteria filter, atau database desa masih kosong.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
