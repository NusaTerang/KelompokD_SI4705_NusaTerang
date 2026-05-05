@extends('layouts.admin')

@section('breadcrumbs')
    <span class="hover:text-secondary cursor-pointer">Dashboard</span>
    <span class="material-symbols-outlined text-xs mx-1">chevron_right</span>
    <span class="hover:text-secondary cursor-pointer">Proyek Energi</span>
    <span class="material-symbols-outlined text-xs mx-1">chevron_right</span>
    <span class="text-secondary font-bold">Review &amp; Simpan</span>
@endsection

@section('page_heading', 'Review & Kirim Proyek')

@section('content')
    {{-- Step Indicator --}}
    <div class="max-w-4xl mx-auto mb-12">
        <div class="flex items-center justify-between relative">
            <div class="absolute top-1/2 left-0 w-full h-[2px] bg-surface-container-high -z-10 -translate-y-1/2"></div>

            <div class="flex flex-col items-center gap-3 bg-background px-4">
                <div class="w-10 h-10 rounded-full bg-tertiary flex items-center justify-center text-on-tertiary shadow-lg">
                    <span class="material-symbols-outlined text-xl">check</span>
                </div>
                <span class="text-xs font-semibold uppercase tracking-widest text-on-surface-variant">Detail Proyek</span>
            </div>

            <div class="flex flex-col items-center gap-3 bg-background px-4">
                <div class="w-10 h-10 rounded-full bg-tertiary flex items-center justify-center text-on-tertiary shadow-lg">
                    <span class="material-symbols-outlined text-xl">check</span>
                </div>
                <span class="text-xs font-semibold uppercase tracking-widest text-on-surface-variant">Pilih Penyedia</span>
            </div>

            <div class="flex flex-col items-center gap-3 bg-background px-4">
                <div class="w-10 h-10 rounded-full bg-solar-gold flex items-center justify-center text-secondary shadow-xl ring-4 ring-solar-gold/30">
                    <span class="font-bold text-lg">3</span>
                </div>
                <span class="text-xs font-bold uppercase tracking-widest text-secondary">Review &amp; Simpan</span>
            </div>
        </div>
    </div>

    {{-- Review Card --}}
    <section class="max-w-[800px] mx-auto">
        <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-slate-100 overflow-hidden">

            {{-- Card Header --}}
            <div class="p-8 lg:p-10">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <span class="inline-flex items-center gap-2 px-3 py-1 bg-secondary-fixed text-on-secondary-container rounded-full text-[10px] font-bold uppercase tracking-widest mb-3">
                            <span class="material-symbols-outlined text-sm">location_on</span>
                            {{ $proyek->desa->nama_desa ?? 'Desa Tidak Diketahui' }}, {{ $proyek->desa->provinsi ?? '' }}
                        </span>
                        <h1 class="text-3xl font-extrabold text-on-surface tracking-tight headline-font">{{ $proyek->judul }}</h1>
                    </div>
                    @if($proyek->target_dana)
                    <div class="text-right shrink-0 ml-6">
                        <p class="text-on-surface-variant text-xs uppercase font-bold tracking-widest mb-1">Target Dana</p>
                        <p class="text-2xl font-black text-primary tracking-tight">Rp {{ number_format($proyek->target_dana, 0, ',', '.') }}</p>
                    </div>
                    @endif
                </div>

                {{-- Photo + Info Grid --}}
                <div class="grid grid-cols-12 gap-6 mb-10">
                    <div class="col-span-12 md:col-span-7 aspect-[16/10] rounded-xl overflow-hidden shadow-sm bg-surface-container-low flex items-center justify-center">
                        @if($proyek->fotos && $proyek->fotos->count() > 0)
                            <img src="{{ str_starts_with($proyek->fotos->first()->path, 'http') ? $proyek->fotos->first()->path : asset('storage/' . $proyek->fotos->first()->path) }}"
                                 alt="Foto Proyek" class="w-full h-full object-cover">
                        @else
                            <span class="material-symbols-outlined text-5xl text-on-surface-variant/30">image</span>
                        @endif
                    </div>
                    <div class="col-span-12 md:col-span-5 grid grid-rows-2 gap-6">
                        <div class="bg-surface-container-low p-6 rounded-xl flex flex-col justify-center">
                            <span class="material-symbols-outlined text-secondary mb-2">calendar_today</span>
                            <p class="text-xs text-on-surface-variant font-medium">Estimasi Pengerjaan</p>
                            <p class="text-lg font-bold">
                                @if($proyek->estimasi_mulai)
                                    {{ $proyek->estimasi_mulai->translatedFormat('d M Y') }}
                                @else
                                    <span class="text-on-surface-variant/50">Belum ditentukan</span>
                                @endif
                            </p>
                        </div>
                        <div class="bg-surface-container-low p-6 rounded-xl flex flex-col justify-center">
                            <span class="material-symbols-outlined text-tertiary mb-2">bolt</span>
                            <p class="text-xs text-on-surface-variant font-medium">Jenis Energi</p>
                            <p class="text-lg font-bold">{{ ucfirst(str_replace('_', ' ', $proyek->jenis_energi ?? '-')) }}</p>
                        </div>
                    </div>
                </div>

                {{-- Description --}}
                @if($proyek->deskripsi)
                <div class="mb-10">
                    <h3 class="text-sm font-bold text-on-surface uppercase tracking-widest mb-4 flex items-center gap-2">
                        Deskripsi Proyek
                        <div class="h-[1px] flex-1 bg-outline-variant/30 ml-2"></div>
                    </h3>
                    <p class="text-on-surface-variant leading-relaxed">{{ $proyek->deskripsi }}</p>
                </div>
                @endif

                {{-- Selected Provider --}}
                @if($proyek->penyedia)
                <div class="bg-surface-container-low p-8 rounded-xl border border-outline-variant/10">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xs font-bold text-secondary uppercase tracking-widest">Penyedia Terpilih</h3>
                        <a href="{{ route('proyek.step2', $proyek->id) }}" class="text-secondary text-sm font-medium hover:underline flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">edit</span> Edit
                        </a>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-center gap-6">
                        <div class="w-16 h-16 bg-white rounded-xl shadow-sm flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-3xl text-secondary" style="font-variation-settings: 'FILL' 1;">solar_power</span>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-xl font-bold text-on-surface mb-1">{{ $proyek->penyedia->nama }}</h4>
                            <p class="text-sm text-on-surface-variant">
                                {{ ucfirst(str_replace('_', ' ', $proyek->penyedia->spesialisasi ?? '')) }}
                            </p>
                        </div>
                        <div class="md:text-right shrink-0">
                            <p class="text-[10px] text-on-surface-variant uppercase font-bold tracking-widest mb-1">Kisaran Harga</p>
                            <p class="text-lg font-black text-secondary">
                                Rp {{ number_format($proyek->penyedia->kisaran_harga_min, 0, ',', '.') }}
                                — Rp {{ number_format($proyek->penyedia->kisaran_harga_max, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
                @else
                <div class="bg-surface-container-low p-8 rounded-xl border border-outline-variant/10 text-center">
                    <span class="material-symbols-outlined text-4xl text-on-surface-variant/30 mb-2">factory</span>
                    <p class="text-on-surface-variant font-medium">Belum ada penyedia dipilih.</p>
                    <a href="{{ route('proyek.step2', $proyek->id) }}" class="inline-flex items-center gap-1 text-secondary font-bold text-sm mt-2 hover:underline">
                        <span class="material-symbols-outlined text-sm">add</span> Pilih Penyedia
                    </a>
                </div>
                @endif
            </div>

            {{-- Footer Actions --}}
            <div class="px-8 py-8 bg-surface-container-high/30 flex flex-col sm:flex-row justify-between items-center gap-4">
                <form action="{{ route('proyek.save.draft', $proyek->id) }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full sm:w-auto px-8 py-3 rounded-xl font-bold text-on-surface-variant hover:bg-surface-container transition-all active:scale-95 duration-200 text-center">
                        Simpan sebagai Draft
                    </button>
                </form>
                <form action="{{ route('proyek.kirim', $proyek->id) }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full sm:w-auto px-8 py-4 rounded-xl bg-solar-gold text-secondary font-extrabold text-sm shadow-lg shadow-solar-gold/20 hover:bg-primary-fixed-dim transition-all active:scale-95 flex items-center justify-center gap-2">
                        Kirim ke Penyedia
                        <span class="material-symbols-outlined text-lg">send</span>
                    </button>
                </form>
            </div>
        </div>

        <p class="text-center text-on-surface-variant/60 text-xs mt-8 px-4">
            Dengan menekan "Kirim ke Penyedia", Anda menyetujui syarat dan ketentuan platform NusaTerang terkait proses pengajuan proyek energi baru.
        </p>
    </section>
@endsection
