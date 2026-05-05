@extends('layouts.admin')

@section('breadcrumbs')
    <span>Dashboard</span>
    <span class="mx-1 text-slate-400">/</span>
    <span>Proyek Energi</span>
    <span class="mx-1 text-slate-400">/</span>
    <span class="font-medium text-slate-700">Buat Proyek — Step 2</span>
@endsection

@section('page_heading', 'Pilih Penyedia Energi')

@section('content')
    <div class="max-w-[800px] mx-auto mb-12">
        <div class="flex items-center justify-between relative">
            <div class="absolute top-1/2 left-0 w-full h-1 bg-surface-container -translate-y-1/2 -z-10"></div>
            <div class="flex flex-col items-center gap-3 bg-surface">
                <div class="w-10 h-10 rounded-full bg-sustainability-green text-white flex items-center justify-center font-bold ring-4 ring-surface shadow-sm">
                    <span class="material-symbols-outlined text-sm">check</span>
                </div>
                <span class="text-sm font-bold text-on-surface">Detail Proyek</span>
            </div>
            <div class="flex flex-col items-center gap-3 bg-surface">
                <div class="w-10 h-10 rounded-full bg-solar-gold text-deep-navy flex items-center justify-center font-bold ring-4 ring-surface">
                    2
                </div>
                <span class="text-sm font-bold text-on-surface">Pilih Penyedia</span>
            </div>
            <div class="flex flex-col items-center gap-3 bg-surface">
                <div class="w-10 h-10 rounded-full bg-surface-container-highest text-on-surface-variant flex items-center justify-center font-bold ring-4 ring-surface">
                    3
                </div>
                <span class="text-sm font-medium text-on-surface-variant">Review & Simpan</span>
            </div>
        </div>
    </div>

    <form action="{{ route('proyek.save.step2', $proyek->id) }}" method="POST" class="max-w-[1000px] mx-auto">
        @csrf

        <div class="mb-8">
            <h2 class="text-3xl font-extrabold text-deep-navy font-headline">Rekomendasi Penyedia Energi</h2>
            <p class="text-on-surface-variant mt-2">Daftar penyedia terbaik yang sesuai dengan profil desa berdasarkan AI.</p>
        </div>

        @if ($errors->any())
            <div class="p-4 bg-red-100 text-red-700 rounded-lg mb-6">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($recommendations->isEmpty())
            <div class="text-center py-16 bg-white rounded-2xl border border-surface-container mb-8">
                <span class="material-symbols-outlined text-5xl text-on-surface-variant/30 mb-3 block">search_off</span>
                <p class="text-on-surface-variant font-medium">Tidak ada penyedia yang memenuhi kriteria.</p>
                <p class="text-sm text-on-surface-variant/70 mt-1">Coba ubah jenis energi atau pastikan data desa sudah lengkap.</p>
            </div>
        @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            @foreach($recommendations as $index => $penyedia)
                <label class="relative block cursor-pointer group" data-spesialisasi="{{ $penyedia->spesialisasi }}">
                    <input type="radio" name="penyedia_id_radio" value="{{ $penyedia->id }}" class="peer sr-only" {{ old('penyedia_id', $proyek->penyedia_id) == $penyedia->id ? 'checked' : '' }}>
                    <div class="bg-white rounded-2xl p-6 border-2 border-transparent peer-checked:border-primary-container peer-checked:bg-primary-container/5 transition-all shadow-[0_8px_30px_rgba(0,0,0,0.04)] hover:shadow-[0_20px_40px_rgba(0,0,0,0.08)]">

                        @if($index === 0)
                            <div class="absolute -top-3 -right-3 bg-solar-gold text-deep-navy text-[10px] font-black px-3 py-1.5 rounded-full shadow-md flex items-center gap-1 z-10">
                                <span class="material-symbols-outlined text-[14px]">star</span>
                                #1 REKOMENDASI TERBAIK
                            </div>
                        @endif

                        <div class="absolute inset-0 opacity-0 peer-checked:opacity-100 pointer-events-none rounded-2xl flex items-center justify-center bg-primary-container/5 transition-opacity">
                            <span class="material-symbols-outlined absolute top-4 right-4 text-primary-container text-2xl" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                        </div>

                        <div class="flex gap-5">
                            <div class="w-16 h-16 rounded-xl bg-surface-container-low flex items-center justify-center shrink-0 border border-surface-container">
                                <span class="material-symbols-outlined text-3xl text-primary" style="font-variation-settings: 'FILL' 1;">solar_power</span>
                            </div>

                            <div class="flex-1">
                                <div class="flex justify-between items-start mb-1">
                                    <h3 class="font-bold text-lg text-deep-navy font-headline">{{ $penyedia->nama }}</h3>
                                    <div class="flex items-center gap-1 bg-surface-container-low px-2 py-1 rounded text-xs">
                                        <span class="material-symbols-outlined text-[14px] text-solar-gold" style="font-variation-settings: 'FILL' 1;">star</span>
                                        <span class="font-bold text-deep-navy">{{ $penyedia->rating }}</span>
                                    </div>
                                </div>
                                <p class="text-sm text-on-surface-variant font-medium flex items-center gap-1 mb-4">
                                    <span class="w-2 h-2 rounded-full bg-sustainability-green"></span>
                                    {{ ucfirst(str_replace('_', ' ', $penyedia->spesialisasi)) }} Spesialis
                                </p>

                                <div class="grid grid-cols-3 gap-3">
                                    <div>
                                        <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Skor</p>
                                        <div class="flex items-end gap-1">
                                            <span class="text-xl font-black text-sustainability-green leading-none">{{ round($penyedia->match_score) }}</span>
                                            <span class="text-xs font-medium text-on-surface-variant pb-0.5">/100</span>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Jarak</p>
                                        <p class="font-bold text-deep-navy text-sm">
                                            @if($penyedia->distance_km !== null)
                                                {{ $penyedia->distance_km }} km
                                            @else
                                                <span class="text-on-surface-variant/50">—</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Budget</p>
                                        <p class="font-bold text-deep-navy text-sm">Rp {{ number_format($penyedia->kisaran_harga_min, 0, ',', '.') }}+</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </label>
            @endforeach
        </div>
        @endif

        {{-- Divider --}}
        <div class="my-10 flex items-center gap-4">
            <div class="flex-1 h-px bg-surface-container-high"></div>
            <span class="text-xs font-bold text-on-surface-variant uppercase tracking-widest px-2">Atau Pilih Secara Manual</span>
            <div class="flex-1 h-px bg-surface-container-high"></div>
        </div>

        {{-- Manual Penyedia Selection (always visible) --}}
        <div class="mb-8">
            <div class="mb-6">
                <h2 class="text-3xl font-extrabold text-deep-navy font-headline">Semua Penyedia Aktif</h2>
                <p class="text-on-surface-variant mt-2">Pilih penyedia lain di luar rekomendasi sistem di atas.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($allPenyedia as $p)
                    @php
                        $icon = match($p->spesialisasi) {
                            'mikro_hidro' => 'water',
                            'biogas'      => 'compost',
                            default       => 'solar_power',
                        };
                    @endphp
                    <label class="relative block cursor-pointer group">
                        <input type="radio" name="penyedia_id_radio" value="{{ $p->id }}" class="peer sr-only"
                            {{ old('penyedia_id', $proyek->penyedia_id) == $p->id ? 'checked' : '' }}>
                        <div class="bg-white rounded-2xl p-6 border-2 border-transparent peer-checked:border-primary-container peer-checked:bg-primary-container/5 transition-all shadow-[0_8px_30px_rgba(0,0,0,0.04)] hover:shadow-[0_20px_40px_rgba(0,0,0,0.08)]">

                            <div class="absolute inset-0 opacity-0 peer-checked:opacity-100 pointer-events-none rounded-2xl transition-opacity">
                                <span class="material-symbols-outlined absolute top-4 right-4 text-primary-container text-2xl" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                            </div>

                            <div class="flex gap-5">
                                <div class="w-16 h-16 rounded-xl bg-surface-container-low flex items-center justify-center shrink-0 border border-surface-container">
                                    <span class="material-symbols-outlined text-3xl text-primary" style="font-variation-settings: 'FILL' 1;">{{ $icon }}</span>
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start mb-1">
                                        <h3 class="font-bold text-lg text-deep-navy font-headline">{{ $p->nama }}</h3>
                                        <div class="flex items-center gap-1 bg-surface-container-low px-2 py-1 rounded text-xs">
                                            <span class="material-symbols-outlined text-[14px] text-solar-gold" style="font-variation-settings: 'FILL' 1;">star</span>
                                            <span class="font-bold text-deep-navy">{{ $p->rating }}</span>
                                        </div>
                                    </div>
                                    <p class="text-sm text-on-surface-variant font-medium flex items-center gap-1 mb-4">
                                        <span class="w-2 h-2 rounded-full bg-primary-container"></span>
                                        {{ ucfirst(str_replace('_', ' ', $p->spesialisasi)) }} Spesialis
                                    </p>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Harga Min</p>
                                            <p class="font-bold text-deep-navy text-sm">Rp {{ number_format($p->kisaran_harga_min ?? 0, 0, ',', '.') }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Harga Max</p>
                                            <p class="font-bold text-deep-navy text-sm">Rp {{ number_format($p->kisaran_harga_max ?? 0, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <input type="hidden" name="penyedia_id" id="penyedia_id_hidden" value="{{ old('penyedia_id', $proyek->penyedia_id ?? '') }}">

        <div class="flex justify-between items-center pt-6 border-t border-surface-container relative z-10">
            <a href="{{ route('proyek.create', ['draft_id' => $proyek->id]) }}"
                class="px-8 py-4 rounded-lg font-bold text-on-surface-variant hover:bg-surface-container-low transition-colors">
                Kembali
            </a>
            <div class="flex gap-4">
                <button type="submit"
                    class="bg-solar-gold px-8 py-4 rounded-lg text-deep-navy font-bold shadow-lg hover:brightness-105 active:scale-[0.98] transition-all flex items-center gap-2">
                    Simpan & Review
                    <span class="material-symbols-outlined">arrow_forward</span>
                </button>
            </div>
        </div>
    </form>

    <script>
    (function () {
        const hiddenInput    = document.getElementById('penyedia_id_hidden');
        const radios = document.querySelectorAll('input[type="radio"][name="penyedia_id_radio"]');

        // All radios (recommendation + manual) share the same name, browser handles mutual exclusion.
        radios.forEach(function (radio) {
            radio.addEventListener('change', function () {
                if (this.checked) hiddenInput.value = this.value;
            });
        });

        // Init: sync hidden input from whichever radio is already checked
        radios.forEach(function (radio) {
            if (radio.checked) hiddenInput.value = radio.value;
        });
    })();
    </script>
@endsection
