@extends('layouts.app')

@section('content')

@php
    $spesialisasiLabel = match($provider->spesialisasi) {
        'solar'       => 'Solar Energy',
        'mikro_hidro' => 'Mikro Hidro',
        default       => 'Energi Lainnya',
    };
    $spesialisasiIcon = match($provider->spesialisasi) {
        'solar'       => 'solar_power',
        'mikro_hidro' => 'water_drop',
        default       => 'air',
    };
@endphp

<main class="pt-28 pb-12 px-4 md:px-8 max-w-7xl mx-auto flex-grow w-full">

    {{-- Breadcrumb --}}
    <nav class="flex items-center space-x-2 text-sm text-on-surface-variant mb-6 font-medium">
        <a href="{{ route('penyedia.daftar') }}" class="hover:text-secondary transition-colors">Penyedia Energi</a>
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
        <span class="text-on-surface">Detail Profil</span>
    </nav>

    <div class="flex justify-between items-end mb-8">
        <div>
            <h2 class="text-3xl font-headline font-extrabold text-on-surface tracking-tight">Detail Penyedia Energi</h2>
            <p class="text-on-surface-variant mt-2">Menampilkan informasi lengkap penyedia layanan energi.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Left Column --}}
        <div class="lg:col-span-2 space-y-8">

            {{-- Informasi Perusahaan --}}
            <section class="bg-white rounded-xl p-8 shadow-sm border border-surface-variant/30">
                <div class="flex items-center gap-3 mb-6">
                    <span class="material-symbols-outlined text-primary bg-primary-container/20 p-2 rounded-lg">business</span>
                    <h3 class="text-xl font-headline font-bold text-on-surface">Informasi Perusahaan</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <p class="text-xs text-on-surface-variant uppercase tracking-wider mb-1 font-semibold">Nama Perusahaan</p>
                        <p class="text-lg font-medium text-on-surface">{{ $provider->nama }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-variant uppercase tracking-wider mb-1 font-semibold">Spesialisasi</p>
                        <div class="inline-flex items-center gap-1 bg-primary-container/20 text-on-surface px-3 py-1 rounded-full text-sm font-medium">
                            <span class="material-symbols-outlined text-[16px] text-primary">{{ $spesialisasiIcon }}</span>
                            {{ $spesialisasiLabel }}
                        </div>
                    </div>
                    @if($provider->no_telepon)
                    <div>
                        <p class="text-xs text-on-surface-variant uppercase tracking-wider mb-1 font-semibold">Nomor Kontak</p>
                        <p class="text-base text-on-surface flex items-center gap-2">
                            <span class="material-symbols-outlined text-on-surface-variant text-[18px]">call</span>
                            {{ $provider->no_telepon }}
                        </p>
                    </div>
                    @endif
                    @if($provider->email)
                    <div>
                        <p class="text-xs text-on-surface-variant uppercase tracking-wider mb-1 font-semibold">Email</p>
                        <p class="text-base text-on-surface flex items-center gap-2">
                            <span class="material-symbols-outlined text-on-surface-variant text-[18px]">mail</span>
                            {{ $provider->email }}
                        </p>
                    </div>
                    @endif
                    <div>
                        <p class="text-xs text-on-surface-variant uppercase tracking-wider mb-1 font-semibold">Status Kemitraan</p>
                        @if($provider->status === 'aktif')
                            <div class="inline-flex items-center gap-1 bg-tertiary-container/30 text-tertiary px-3 py-1 rounded-full text-sm font-medium">
                                <span class="material-symbols-outlined text-[16px]">verified</span>
                                Aktif
                            </div>
                        @else
                            <div class="inline-flex items-center gap-1 bg-surface-variant text-on-surface-variant px-3 py-1 rounded-full text-sm font-medium">
                                <span class="material-symbols-outlined text-[16px]">block</span>
                                Nonaktif
                            </div>
                        @endif
                    </div>
                </div>

                @if($provider->deskripsi)
                <div>
                    <p class="text-xs text-on-surface-variant uppercase tracking-wider mb-2 font-semibold">Deskripsi Perusahaan</p>
                    <p class="text-on-surface-variant leading-relaxed text-sm bg-surface-container-low p-4 rounded-lg">
                        {{ $provider->deskripsi }}
                    </p>
                </div>
                @endif
            </section>

            {{-- Kapasitas & Layanan --}}
            @if($provider->kapasitas_maks || $provider->kisaran_harga_min || $provider->kisaran_harga_max)
            <section class="bg-white rounded-xl p-8 shadow-sm border border-surface-variant/30">
                <div class="flex items-center gap-3 mb-6">
                    <span class="material-symbols-outlined text-primary bg-primary-container/20 p-2 rounded-lg">electric_meter</span>
                    <h3 class="text-xl font-headline font-bold text-on-surface">Kapasitas &amp; Layanan</h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    @if($provider->kapasitas_maks)
                    <div class="bg-surface-container-low p-5 rounded-lg border border-outline-variant/20">
                        <div class="flex justify-between items-start mb-2">
                            <p class="text-sm text-on-surface-variant font-medium">Kapasitas Daya Maksimal</p>
                            <span class="material-symbols-outlined text-on-surface-variant">battery_charging_full</span>
                        </div>
                        <p class="text-3xl font-headline font-extrabold text-on-surface">
                            {{ number_format($provider->kapasitas_maks, 0, ',', '.') }}
                            <span class="text-lg text-on-surface-variant font-medium">kWh</span>
                        </p>
                    </div>
                    @endif
                    @if($provider->kisaran_harga_min || $provider->kisaran_harga_max)
                    <div class="bg-surface-container-low p-5 rounded-lg border border-outline-variant/20">
                        <div class="flex justify-between items-start mb-2">
                            <p class="text-sm text-on-surface-variant font-medium">Rentang Harga Layanan</p>
                            <span class="material-symbols-outlined text-on-surface-variant">payments</span>
                        </div>
                        <p class="text-xl font-headline font-bold text-on-surface">
                            @if($provider->kisaran_harga_min && $provider->kisaran_harga_max)
                                Rp {{ number_format($provider->kisaran_harga_min, 0, ',', '.') }} – Rp {{ number_format($provider->kisaran_harga_max, 0, ',', '.') }}
                            @elseif($provider->kisaran_harga_min)
                                Rp {{ number_format($provider->kisaran_harga_min, 0, ',', '.') }}+
                            @else
                                s.d. Rp {{ number_format($provider->kisaran_harga_max, 0, ',', '.') }}
                            @endif
                        </p>
                        <p class="text-xs text-on-surface-variant mt-1">per kWh (Estimasi)</p>
                    </div>
                    @endif
                </div>
            </section>
            @endif

        </div>

        {{-- Right Column --}}
        <div class="space-y-8">

            {{-- Lokasi Operasional --}}
            <section class="bg-white rounded-xl shadow-sm border border-surface-variant/30 overflow-hidden">
                {{-- Map placeholder --}}
                <div class="h-48 bg-surface-container-high relative flex items-center justify-center">
                    <span class="material-symbols-outlined text-5xl text-secondary opacity-30">map</span>
                    @if($provider->latitude && $provider->longitude)
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="w-4 h-4 bg-primary rounded-full border-2 border-white shadow-md animate-pulse"></div>
                        </div>
                    @endif
                </div>

                <div class="p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="material-symbols-outlined text-secondary bg-secondary-container/20 p-2 rounded-lg">map</span>
                        <h3 class="text-lg font-headline font-bold text-on-surface">Lokasi Operasional</h3>
                    </div>
                    <ul class="space-y-4">
                        @if($provider->provinsi_operasi)
                        <li class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-on-surface-variant text-[20px] mt-0.5">location_city</span>
                            <div>
                                <p class="text-xs text-on-surface-variant font-semibold uppercase tracking-wider">Provinsi / Kota</p>
                                <p class="text-sm text-on-surface font-medium">
                                    {{ $provider->provinsi_operasi }}{{ $provider->kota ? ', ' . $provider->kota : '' }}
                                </p>
                            </div>
                        </li>
                        @endif
                        @if($provider->latitude && $provider->longitude)
                        <li class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-on-surface-variant text-[20px] mt-0.5">my_location</span>
                            <div class="w-full">
                                <p class="text-xs text-on-surface-variant font-semibold uppercase tracking-wider mb-1">Koordinat</p>
                                <div class="flex justify-between items-center bg-surface-container-low px-3 py-2 rounded-md">
                                    <span class="text-xs font-mono text-on-surface-variant">Lat: {{ $provider->latitude }}</span>
                                    <span class="text-xs font-mono text-on-surface-variant">Lng: {{ $provider->longitude }}</span>
                                </div>
                            </div>
                        </li>
                        @endif
                    </ul>
                </div>
            </section>

            {{-- Rating --}}
            @if($provider->rating > 0)
            <section class="bg-white rounded-xl p-6 shadow-sm border border-surface-variant/30">
                <h3 class="text-md font-headline font-bold text-on-surface mb-4">Rating</h3>
                <div class="flex items-center gap-3">
                    <span class="text-4xl font-headline font-extrabold text-on-surface">{{ $provider->rating }}</span>
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center gap-0.5">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="material-symbols-outlined text-[18px] {{ $i <= $provider->rating ? 'text-primary' : 'text-outline-variant' }}"
                                      style="{{ $i <= $provider->rating ? "font-variation-settings: 'FILL' 1;" : '' }}">star</span>
                            @endfor
                        </div>
                        <p class="text-xs text-on-surface-variant">dari 5.0</p>
                    </div>
                </div>
            </section>
            @endif

        </div>

    </div>

    {{-- Back Button --}}
    <div class="mt-8">
        <a href="{{ route('penyedia.daftar') }}"
           class="inline-flex items-center gap-2 text-sm font-semibold text-secondary hover:underline">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Kembali ke Daftar Penyedia
        </a>
    </div>

</main>

@endsection
