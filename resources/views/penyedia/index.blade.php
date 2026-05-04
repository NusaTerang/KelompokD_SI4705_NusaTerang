@extends('layouts.app')

@section('content')

    {{-- Hero --}}
    <section class="bg-secondary pt-20 pb-32 flex flex-col items-center justify-center relative overflow-hidden text-center px-4">
        <div class="absolute top-10 left-1/2 -translate-x-1/2 w-full max-w-4xl h-[300px] pointer-events-none opacity-10"
             style="background: radial-gradient(ellipse at top, #F9D423 0%, transparent 50%);"></div>
        <div class="relative z-10 max-w-3xl flex flex-col items-center gap-4">
            <h1 class="text-white text-4xl md:text-5xl font-headline font-extrabold tracking-tight">
                Penyedia Energi Terpercaya
            </h1>
            <p class="text-white/80 text-lg font-normal max-w-2xl leading-relaxed">
                Mendukung transisi energi berkelanjutan melalui kemitraan strategis dengan penyedia infrastruktur panel surya dan solusi terbarukan terbaik di Indonesia.
            </p>
        </div>
    </section>

    {{-- Main Content - overlaps hero --}}
    <section class="w-full max-w-7xl mx-auto px-4 -mt-16 relative z-20 flex flex-col gap-8 pb-12">

        {{-- Filter & Search Bar --}}
        <form action="{{ url('/penyedia/daftar') }}" method="GET"
              class="w-full bg-white rounded-2xl shadow-lg border border-surface-container p-6 lg:p-8 flex flex-col lg:flex-row items-center justify-between gap-6">

            <div class="relative w-full lg:max-w-lg">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-outline-variant">
                    <span class="material-symbols-outlined">search</span>
                </div>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama penyedia atau wilayah..."
                    class="w-full bg-surface-container-low rounded-xl py-3.5 pl-12 pr-4 outline-none text-on-surface focus:ring-2 focus:ring-secondary transition-shadow border-none"
                />
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-4 w-full lg:w-auto">

                <div class="flex items-center bg-surface-container-low p-1 rounded-xl w-full sm:w-auto shrink-0">
                    <a href="{{ url('/penyedia/daftar') }}{{ request('search') ? '?search='.e(request('search')) : '' }}"
                       class="{{ !request('status') ? 'flex-1 sm:flex-none px-6 py-2.5 bg-white text-secondary text-sm font-bold rounded-lg shadow-sm' : 'flex-1 sm:flex-none px-6 py-2.5 text-on-surface-variant text-sm font-medium rounded-lg hover:text-on-surface transition-colors' }}">
                        Semua
                    </a>
                    <a href="{{ url('/penyedia/daftar') }}?status=aktif{{ request('search') ? '&search='.e(request('search')) : '' }}"
                       class="{{ request('status') == 'aktif' ? 'flex-1 sm:flex-none px-6 py-2.5 bg-white text-secondary text-sm font-bold rounded-lg shadow-sm' : 'flex-1 sm:flex-none px-6 py-2.5 text-on-surface-variant text-sm font-medium rounded-lg hover:text-on-surface transition-colors' }}">
                        Aktif
                    </a>
                </div>

                <a href="{{ route('proyek.create') }}"
                   class="flex items-center justify-center gap-2 w-full sm:w-auto bg-primary-container text-on-primary-fixed px-6 py-3.5 rounded-xl font-headline font-bold hover:opacity-90 transition-all shadow-sm shrink-0">
                    <span class="material-symbols-outlined text-[20px]">add_circle</span>
                    Mulai Proyek Baru
                </a>
            </div>
        </form>

        {{-- Provider Grid --}}
        @if($providers->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($providers as $provider)
                    @php
                        $isActive = $provider->status === 'aktif';
                        $words    = collect(explode(' ', $provider->nama))->filter()->take(2);
                        $initials = $words->map(fn($w) => strtoupper($w[0]))->join('');
                        $spLabel  = match($provider->spesialisasi) {
                            'solar'      => 'Panel Surya',
                            'mikro_hidro'=> 'Mikro Hidro',
                            default      => 'Energi Lainnya',
                        };
                    @endphp

                    <div class="w-full bg-white rounded-2xl shadow-sm border border-surface-container p-6 flex flex-col gap-4 hover:shadow-md transition-shadow">

                        {{-- Logo & Status --}}
                        <div class="flex justify-between items-start w-full">
                            <div class="w-16 h-16 rounded-xl bg-secondary flex items-center justify-center shrink-0">
                                <span class="text-white font-headline font-bold text-xl">{{ $initials }}</span>
                            </div>
                            <div class="flex items-center gap-1.5 px-3 py-1 rounded-full {{ $isActive ? 'bg-tertiary-container/50' : 'bg-surface-container' }}">
                                <div class="w-1.5 h-1.5 rounded-full {{ $isActive ? 'bg-tertiary' : 'bg-outline' }}"></div>
                                <span class="text-xs font-bold tracking-wider {{ $isActive ? 'text-tertiary' : 'text-outline' }}">
                                    {{ strtoupper($provider->status) }}
                                </span>
                            </div>
                        </div>

                        {{-- Name --}}
                        <h3 class="text-xl font-headline font-semibold text-on-surface leading-tight">
                            {{ $provider->nama }}
                        </h3>

                        {{-- Contact --}}
                        <div class="flex flex-col gap-2 w-full">
                            @if($provider->provinsi_operasi)
                                <div class="flex items-center gap-2 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-[16px]">location_on</span>
                                    <span class="truncate">{{ $provider->provinsi_operasi }}</span>
                                </div>
                            @endif
                            @if($provider->email)
                                <div class="flex items-center gap-2 text-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-[16px]">mail</span>
                                    <span class="truncate">{{ $provider->email }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Tags --}}
                        <div class="flex flex-wrap gap-2 w-full py-1">
                            <div class="bg-surface-container px-3 py-1 rounded-lg">
                                <span class="text-on-surface-variant text-xs font-semibold">{{ $spLabel }}</span>
                            </div>
                            @if($provider->rating > 0)
                                <div class="bg-primary-container/30 px-3 py-1 rounded-lg flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[12px] text-primary" style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="text-primary text-xs font-semibold">{{ $provider->rating }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="flex flex-col gap-3 w-full mt-auto pt-2">
                            <a href="#" class="w-full py-3 rounded-xl border-2 border-secondary text-secondary font-headline font-bold hover:bg-secondary hover:text-white transition-all text-center">
                                Lihat Profil
                            </a>

                            @if($isActive)
                                <a href="{{ route('proyek.create') }}"
                                   class="w-full py-3 rounded-xl bg-primary-container text-on-primary-fixed font-headline font-bold flex items-center justify-center gap-2 hover:opacity-90 transition-all shadow-sm">
                                    <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                    Assign ke Proyek
                                </a>
                            @else
                                <button disabled class="w-full py-3 rounded-xl bg-surface-container text-outline font-headline font-bold flex items-center justify-center gap-2 cursor-not-allowed">
                                    <span class="material-symbols-outlined text-sm">cancel</span>
                                    Tidak Tersedia
                                </button>
                            @endif
                        </div>

                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-8 flex justify-center">
                {{ $providers->links() }}
            </div>

        @else
            <div class="py-20 text-center flex flex-col items-center justify-center bg-white rounded-2xl border border-dashed border-outline-variant">
                <span class="material-symbols-outlined text-[64px] text-outline-variant mb-4">business_center</span>
                <h3 class="text-xl font-headline font-bold text-on-surface mb-2">Tidak ada penyedia ditemukan</h3>
                <p class="text-on-surface-variant max-w-md">Belum ada penyedia energi yang sesuai dengan pencarian Anda.</p>
            </div>
        @endif

    </section>

@endsection
