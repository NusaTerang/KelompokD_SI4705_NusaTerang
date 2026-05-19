@extends('layouts.penyedia')

@section('title', 'Update Progress Proyek')

@section('breadcrumbs')
    <a href="{{ route('vendor.dashboard') }}" class="text-on-surface-variant hover:text-secondary">Dashboard</a>
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <a href="{{ route('vendor.proyek.index') }}" class="text-on-surface-variant hover:text-secondary">Proyek Energi</a>
    <span class="material-symbols-outlined text-xs">chevron_right</span>
    <span class="text-[#0F4C81] font-bold">Update Progress</span>
@endsection

@push('head')
<style>
    input[type=range] {
        -webkit-appearance: none;
        width: 100%;
        background: transparent;
    }
    input[type=range]::-webkit-slider-runnable-track {
        width: 100%;
        height: 8px;
        cursor: pointer;
        background: #ebeeed;
        border-radius: 9999px;
    }
    input[type=range]::-webkit-slider-thumb {
        height: 24px;
        width: 24px;
        border-radius: 9999px;
        background: #F9D423;
        cursor: pointer;
        -webkit-appearance: none;
        margin-top: -8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        border: 3px solid white;
    }
</style>
<script src="//unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush

@section('content')
@php
    $currentPercentage = old('persentase', $draft->persentase ?? ($updates->first()?->persentase ?? 0));
    $currentStatus = old('status_progress', $draft->status_progress ?? 'berjalan');
    $statusLabels = [
        'dijadwalkan' => 'Dijadwalkan',
        'berjalan' => 'Berjalan',
        'selesai' => 'Selesai',
    ];
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-10">
        <h1 class="text-4xl font-extrabold text-[#0F4C81] tracking-tight mb-2">Update Progress Proyek</h1>
        <p class="text-on-surface-variant">Catat perkembangan terkini implementasi energi terbarukan di lapangan.</p>
    </div>

    @if($draft)
        <div class="rounded-2xl border border-[#F9D423]/30 bg-[#F9D423]/10 p-5 text-[#0F4C81] mb-8">
            <p class="font-bold">Draft progress tersimpan.</p>
            <p class="text-sm mt-1">Lanjutkan pengisian lalu kirim update saat siap.</p>
        </div>
    @endif

    <div class="grid grid-cols-12 gap-10 items-start">
        <div class="col-span-12 lg:col-span-7 space-y-8">
            <div class="bg-surface-container-lowest rounded-2xl shadow-sm overflow-hidden border border-slate-100">
                <div class="px-8 py-5 bg-surface-container-low flex flex-col sm:flex-row gap-4 sm:justify-between sm:items-center border-b border-slate-100">
                    <div>
                        <h3 class="font-bold text-[#0F4C81]">{{ $proyek->judul }}</h3>
                        <p class="text-xs text-on-surface-variant flex items-center gap-1 mt-1">
                            <span class="material-symbols-outlined text-xs">location_on</span>
                            {{ $proyek->desa->nama_desa ?? 'Desa belum tersedia' }}{{ $proyek->desa?->provinsi ? ', ' . $proyek->desa->provinsi : '' }}
                        </p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <span class="block text-2xl font-extrabold text-[#0F4C81]">{{ $currentPercentage }}%</span>
                            <span class="text-[10px] uppercase font-bold text-on-surface-variant tracking-wider leading-none">Selesai</span>
                        </div>
                        <span class="bg-[#F9D423] text-[#0F4C81] px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider">{{ strtoupper($statusLabels[$currentStatus] ?? 'BERJALAN') }}</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('vendor.proyek.progress.store', $penugasan->id_penugasan) }}" enctype="multipart/form-data" class="p-8 space-y-8" x-data="{ percentage: {{ (int) $currentPercentage }} }">
                    @csrf

                    <div class="space-y-4">
                        <label for="persentase" class="block text-xs font-bold text-[#0F4C81] uppercase tracking-widest">Persentase Penyelesaian</label>
                        <div class="text-center py-4">
                            <span class="text-6xl font-black text-[#0F4C81] inline-flex items-end">
                                <span x-text="percentage"></span><span class="text-3xl font-bold text-[#F9D423] mb-2 ml-1">%</span>
                            </span>
                        </div>
                        <div class="relative px-2">
                            <input id="persentase" name="persentase" class="w-full" max="100" min="0" type="range" x-model="percentage" value="{{ $currentPercentage }}">
                            <div class="flex justify-between mt-2 text-[10px] font-bold text-on-surface-variant uppercase tracking-wider">
                                <span>0%</span>
                                <span>25%</span>
                                <span>50%</span>
                                <span>75%</span>
                                <span>100%</span>
                            </div>
                        </div>
                        @error('persentase')
                            <p class="text-sm text-error mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-3">
                        <label for="deskripsi" class="block text-xs font-bold text-[#0F4C81] uppercase tracking-widest">Keterangan Update</label>
                        <textarea id="deskripsi" name="deskripsi" rows="4" class="w-full bg-surface-container-low border-none rounded-xl p-4 focus:ring-2 focus:ring-[#F9D423] text-on-surface placeholder:text-on-surface-variant/50" placeholder="Deskripsikan perkembangan di lapangan...">{{ old('deskripsi', $draft->deskripsi ?? '') }}</textarea>
                        @error('deskripsi')
                            <p class="text-sm text-error mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-3">
                        <label for="fotos" class="block text-xs font-bold text-[#0F4C81] uppercase tracking-widest">Upload Foto Lapangan <span class="text-[10px] font-normal normal-case text-on-surface-variant">(Maks 5)</span></label>
                        <div class="grid grid-cols-4 gap-4">
                            <label for="fotos" class="aspect-square rounded-xl border-2 border-dashed border-outline-variant flex flex-col items-center justify-center text-on-surface-variant hover:bg-surface-container transition-colors cursor-pointer group">
                                <span class="material-symbols-outlined text-3xl mb-1 group-hover:scale-110 transition-transform">add_a_photo</span>
                                <span class="text-[10px] font-bold">TAMBAH</span>
                            </label>
                            @foreach(($draft->foto_paths ?? []) as $path)
                                <div class="relative group aspect-square rounded-xl overflow-hidden">
                                    <img class="w-full h-full object-cover" src="{{ asset('storage/' . $path) }}" alt="Foto draft progress">
                                </div>
                            @endforeach
                            @for($i = count($draft->foto_paths ?? []); $i < 3; $i++)
                                <div class="aspect-square rounded-xl bg-surface-container-low border border-outline-variant/30 flex items-center justify-center italic text-[10px] text-on-surface-variant">Kosong</div>
                            @endfor
                        </div>
                        <input id="fotos" name="fotos[]" type="file" multiple accept="image/*" class="sr-only">
                        @error('fotos')
                            <p class="text-sm text-error mt-2">{{ $message }}</p>
                        @enderror
                        @error('fotos.*')
                            <p class="text-sm text-error mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-4">
                        <label class="block text-xs font-bold text-[#0F4C81] uppercase tracking-widest">Status Instalasi</label>
                        <div class="flex flex-wrap gap-4">
                            @foreach(['dijadwalkan' => 'Dijadwalkan', 'berjalan' => 'Berjalan', 'selesai' => 'Selesai'] as $value => $label)
                                <label class="relative flex cursor-pointer group">
                                    <input class="sr-only peer" name="status_progress" type="radio" value="{{ $value }}" @checked($currentStatus === $value)>
                                    <div class="px-6 py-3 rounded-xl border-2 border-slate-100 text-on-surface-variant font-bold text-sm peer-checked:border-{{ $value === 'selesai' ? '[#27AE60]' : '[#F9D423]' }} peer-checked:bg-{{ $value === 'selesai' ? '[#27AE60]' : '[#F9D423]' }}/10 peer-checked:text-{{ $value === 'selesai' ? '[#27AE60]' : '[#0F4C81]' }} transition-all">
                                        {{ $label }}
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('status_progress')
                            <p class="text-sm text-error mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-6 border-t border-slate-100">
                        <button class="px-8 py-3 rounded-xl font-bold text-on-surface-variant hover:bg-surface-container transition-all active:scale-95 duration-200" type="submit" name="save_draft" value="1">
                            Simpan Draft
                        </button>
                        <button class="bg-[#F9D423] hover:bg-[#e8c404] text-[#0F4C81] px-10 py-3 rounded-xl font-bold shadow-lg shadow-[#F9D423]/20 transition-all active:scale-95 duration-200" type="submit">
                            Kirim Update
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-span-12 lg:col-span-5 space-y-6">
            <div class="bg-surface-container-low rounded-2xl p-8 border border-slate-100">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-xl font-extrabold text-[#0F4C81]">Riwayat Update</h2>
                    <span class="text-[10px] font-bold text-[#0F4C81] bg-white px-2 py-1 rounded shadow-sm border border-slate-100 uppercase tracking-widest">LOG AKTIVITAS</span>
                </div>

                @if($updates->isEmpty())
                    <div class="rounded-2xl bg-white p-8 text-center border border-slate-100">
                        <span class="material-symbols-outlined text-4xl text-on-surface-variant/40 mb-2">update</span>
                        <p class="font-bold text-on-surface">Belum ada update</p>
                        <p class="text-sm text-on-surface-variant mt-1">Update terkirim akan muncul di riwayat.</p>
                    </div>
                @else
                    <div class="space-y-0 relative">
                        <div class="absolute left-[23px] top-4 bottom-4 w-0.5 bg-[#27AE60]/20"></div>
                        @foreach($updates as $update)
                            <div class="relative pl-14 pb-8 group">
                                <div class="absolute left-0 top-1 w-12 h-12 rounded-full bg-white flex items-center justify-center shadow-sm border border-[#27AE60]/40 z-10">
                                    <span class="text-xs font-black text-[#27AE60]">{{ $update->persentase }}%</span>
                                </div>
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-xs font-bold text-[#0F4C81]">{{ $update->submitted_at?->format('d M Y') ?? $update->created_at?->format('d M Y') }}</span>
                                        <span class="text-[10px] text-on-surface-variant bg-white border border-slate-100 px-2 py-0.5 rounded font-medium">{{ $statusLabels[$update->status_progress] ?? 'Berjalan' }}</span>
                                    </div>
                                    <p class="text-sm text-on-surface-variant leading-snug">{{ $update->deskripsi }}</p>
                                    @if(!empty($update->foto_paths))
                                        <div class="mt-3 flex gap-2 flex-wrap">
                                            @foreach($update->foto_paths as $path)
                                                <div class="w-12 h-12 rounded-lg overflow-hidden bg-slate-200 ring-1 ring-slate-100">
                                                    <img class="w-full h-full object-cover opacity-80" src="{{ asset('storage/' . $path) }}" alt="Foto progress">
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="bg-gradient-to-br from-[#0F4C81] to-[#1a64a3] p-6 rounded-2xl text-white shadow-xl shadow-[#0F4C81]/10">
                <div class="flex items-start gap-4">
                    <div class="bg-white/20 p-2 rounded-lg">
                        <span class="material-symbols-outlined">lightbulb</span>
                    </div>
                    <div>
                        <h4 class="font-bold mb-2">Butuh Bantuan?</h4>
                        <p class="text-xs text-white/80 leading-relaxed">Jika Anda mengalami kendala saat mengunggah foto atau data teknis, hubungi tim dukungan teknis provider.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
