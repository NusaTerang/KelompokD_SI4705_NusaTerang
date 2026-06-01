@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page_heading', 'Dashboard Overview')
@section('breadcrumbs', 'Selamat datang kembali, ' . (auth()->user()->nama ?? 'Admin') . '.')

@section('content')
{{-- Error Alert --}}
@if(isset($error))
    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800 flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-3">
            <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            {{ $error }}
        </div>
        <a href="{{ route('admin.dashboard') }}" class="font-bold underline hover:text-red-900">Muat Ulang</a>
    </div>
@endif

{{-- Header + Filter Periode --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <h1 class="text-xl font-bold text-slate-900">Dashboard Utama</h1>
    <form action="{{ route('admin.dashboard') }}" method="GET" id="filterForm">
        <div class="relative">
            <select name="period" onchange="document.getElementById('filterForm').submit()"
                class="appearance-none rounded-lg border border-slate-300 bg-white pl-4 pr-10 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 focus:border-nt-navy focus:outline-none focus:ring-2 focus:ring-nt-navy/20 cursor-pointer w-full sm:w-auto">
                <option value="today"  {{ ($period ?? '') == 'today'  ? 'selected' : '' }}>Hari Ini</option>
                <option value="7days"  {{ ($period ?? '') == '7days'  ? 'selected' : '' }}>7 Hari Terakhir</option>
                <option value="30days" {{ ($period ?? '') == '30days' ? 'selected' : '' }}>30 Hari Terakhir</option>
                <option value="all"    {{ ($period ?? '') == 'all'    ? 'selected' : '' }}>Semua Waktu</option>
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
            </div>
        </div>
    </form>
</div>

<div class="space-y-6">
    {{-- ═══════════ STAT CARDS ═══════════ --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

        {{-- Card 1: Total Proyek --}}
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="flex items-center justify-between">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-yellow-100 text-yellow-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" /></svg>
                </div>
                <span class="flex items-center text-sm font-semibold {{ ($proyekGrowth ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ ($proyekGrowth ?? 0) > 0 ? '+' : '' }}{{ $proyekGrowth ?? 0 }}%
                </span>
            </div>
            <div class="mt-4">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Proyek</h3>
                <p class="mt-1 text-2xl font-bold text-slate-900">{{ number_format($currentProyek ?? 0) }}</p>
            </div>
        </div>

        {{-- Card 2: Dana Terkumpul --}}
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="flex items-center justify-between">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <span class="flex items-center text-sm font-semibold {{ ($danaGrowth ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ ($danaGrowth ?? 0) > 0 ? '+' : '' }}{{ $danaGrowth ?? 0 }}%
                </span>
            </div>
            <div class="mt-4">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Dana Terkumpul</h3>
                <p class="mt-1 text-2xl font-bold text-slate-900">Rp {{ number_format($currentDana ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>

        {{-- Card 3: Desa Terverifikasi --}}
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="flex items-center justify-between">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 text-green-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <span class="flex items-center text-sm font-semibold {{ ($desaGrowth ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ ($desaGrowth ?? 0) > 0 ? '+' : '' }}{{ $desaGrowth ?? 0 }}%
                </span>
            </div>
            <div class="mt-4">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Desa Terverifikasi</h3>
                <p class="mt-1 text-2xl font-bold text-slate-900">{{ number_format($currentDesa ?? 0) }}</p>
            </div>
        </div>

        {{-- Card 4: Donatur Aktif --}}
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="flex items-center justify-between">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-100 text-red-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                </div>
                <span class="flex items-center text-sm font-semibold {{ ($donaturGrowth ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ ($donaturGrowth ?? 0) > 0 ? '+' : '' }}{{ $donaturGrowth ?? 0 }}%
                </span>
            </div>
            <div class="mt-4">
                <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500">Donatur Aktif</h3>
                <p class="mt-1 text-2xl font-bold text-slate-900">{{ number_format($currentDonatur ?? 0) }}</p>
            </div>
        </div>
    </div>

    {{-- ═══════════ KONTEN UTAMA (Proyek Terbaru + Donasi) ═══════════ --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- Proyek Terbaru (kiri, 2 kolom) --}}
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200 lg:col-span-2">
            <div class="mb-6 flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-900">Proyek Terbaru</h2>
            </div>

            <div class="space-y-6">
                @forelse($proyekTerbaru ?? [] as $proyek)
                    @php
                        $target = $proyek->target_dana > 0 ? $proyek->target_dana : 1;
                        $progress = min(100, round(($proyek->dana_terkumpul / $target) * 100));

                        $statusData = match($proyek->status) {
                            'aktif_funding' => ['label' => 'FUNDING',    'color' => 'bg-green-100 text-green-800'],
                            'eksekusi'      => ['label' => 'EKSEKUSI',   'color' => 'bg-blue-100 text-blue-800'],
                            'selesai'       => ['label' => 'SELESAI',    'color' => 'bg-emerald-100 text-emerald-800'],
                            'draft'         => ['label' => 'DRAFT',      'color' => 'bg-slate-200 text-slate-800'],
                            default         => ['label' => strtoupper(str_replace('_', ' ', $proyek->status)), 'color' => 'bg-slate-100 text-slate-700'],
                        };
                    @endphp
                    <div>
                        <div class="mb-2 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h3 class="font-bold text-slate-900">{{ $proyek->judul }}</h3>
                                <p class="text-sm text-slate-500 flex items-center mt-1">
                                    <svg class="mr-1.5 h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    {{ $proyek->desa->kabupaten ?? '-' }}, {{ $proyek->desa->provinsi ?? '' }}
                                </p>
                            </div>
                            <span class="w-fit rounded-full px-3 py-1 text-[10px] font-extrabold tracking-widest {{ $statusData['color'] }}">{{ $statusData['label'] }}</span>
                        </div>
                        <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-nt-accent transition-all duration-500" style="width: {{ $progress }}%"></div>
                        </div>
                        <div class="mt-2 flex items-center justify-between text-sm">
                            <p class="text-slate-500">Terkumpul: <span class="font-bold text-slate-900">Rp {{ number_format($proyek->dana_terkumpul, 0, ',', '.') }}</span></p>
                            <span class="font-bold text-slate-900">{{ $progress }}%</span>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-12 text-center border-2 border-dashed border-slate-100 rounded-xl mt-4">
                        <svg class="h-16 w-16 text-slate-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                        <h3 class="text-sm font-semibold text-slate-900">Belum ada data untuk ditampilkan.</h3>
                        <p class="mt-1 text-sm text-slate-500">Mulai dengan membuat proyek baru.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Aktivitas Donasi (kanan, 1 kolom) --}}
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="mb-6 flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-900">Aktivitas Donasi</h2>
            </div>
            <div class="space-y-5">
                @forelse($donasiTerbaru ?? [] as $donasi)
                    @php $displayName = $donasi->user->nama ?? $donasi->donatur_name ?? 'Donatur'; @endphp
                    <div class="flex items-start gap-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 overflow-hidden">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($displayName) }}&background=0D8ABC&color=fff" alt="" class="h-full w-full object-cover">
                        </div>
                        <div class="flex-1 border-b border-slate-100 pb-4">
                            <div class="flex justify-between">
                                <h4 class="font-semibold text-slate-900">{{ $displayName }}</h4>
                                <span class="font-bold text-green-600">Rp {{ number_format($donasi->total_price, 0, ',', '.') }}</span>
                            </div>
                            <div class="mt-1 flex justify-between text-xs text-slate-500">
                                <p>{{ $donasi->proyek->judul ?? 'Proyek' }}</p>
                                <p>{{ $donasi->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-10 text-center border-t border-slate-100 mt-2">
                        <svg class="h-12 w-12 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <h3 class="text-sm font-semibold text-slate-900">Belum ada donasi</h3>
                        <p class="mt-1 text-sm text-slate-500">Aktivitas donatur akan muncul di sini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
