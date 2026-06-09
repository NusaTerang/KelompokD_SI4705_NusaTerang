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
<style>[x-cloak]{display:none!important}</style>
<script src="//unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush

@section('content')
@php
    $maxProgress = $maxProgress ?? (int) ($updates->max('persentase') ?? 0);
    $allowedOptions = array_values(array_filter([25, 50, 75, 100], fn($v) => $v > $maxProgress));
    $currentStatus = old('status_progress', $draft->status_progress ?? null);
    $statusLabels = [
        'dijadwalkan' => 'Dijadwalkan',
        'berjalan' => 'Berjalan',
        'selesai' => 'Selesai',
    ];
    $draftPhotoUrls = collect($draft?->foto_paths ?? [])->map(fn($p) => asset('storage/' . $p))->values()->all();
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
                            <span class="block text-2xl font-extrabold text-[#0F4C81]">{{ $maxProgress }}%</span>
                            <span class="text-[10px] uppercase font-bold text-on-surface-variant tracking-wider leading-none">Selesai</span>
                        </div>
                        <span class="bg-[#F9D423] text-[#0F4C81] px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider">{{ strtoupper($statusLabels[$currentStatus] ?? 'BERJALAN') }}</span>
                    </div>
                </div>

                <form
                    method="POST"
                    action="{{ route('vendor.proyek.progress.store', $penugasan->id_penugasan) }}"
                    enctype="multipart/form-data"
                    class="p-8 space-y-8"
                    x-data="{
                        persentaseSelected: {{ (empty($allowedOptions) || old('persentase', $draft?->persentase)) ? 'true' : 'false' }},
                        deskripsi: {{ json_encode(old('deskripsi', $draft?->deskripsi ?? '')) }},
                        statusSelected: {{ (old('status_progress') || $draft?->status_progress) ? 'true' : 'false' }},
                        draftPhotos: {{ json_encode($draftPhotoUrls) }},
                        newPreviews: [],
                        attempted: false,
                        get fotoOk() { return this.draftPhotos.length > 0 || this.newPreviews.length > 0; },
                        get displayPhotos() { return this.newPreviews.length > 0 ? this.newPreviews : this.draftPhotos; },
                        get emptySlotList() {
                            const count = Math.max(0, 3 - this.displayPhotos.length);
                            return Array.from({ length: count }, (_, i) => i);
                        },
                        handleFotoChange(e) {
                            const files = Array.from(e.target.files).slice(0, 5);
                            this.newPreviews = files.map(f => URL.createObjectURL(f));
                        },
                        trySubmit() {
                            this.attempted = true;
                            if (this.persentaseSelected && this.deskripsi.trim() && this.statusSelected && this.fotoOk) {
                                this.$el.closest('form').submit();
                            }
                        }
                    }"
                >
                    @csrf

                    {{-- Persentase --}}
                    <div class="space-y-3">
                        <label class="block text-xs font-bold text-[#0F4C81] uppercase tracking-widest">
                            Persentase Penyelesaian <span class="text-red-500">*</span>
                        </label>

                        @if(empty($allowedOptions))
                            <div class="rounded-xl bg-[#27AE60]/10 border border-[#27AE60]/30 px-5 py-4 text-[#27AE60] font-bold text-sm flex items-center gap-2">
                                <span class="material-symbols-outlined text-base">check_circle</span>
                                Progress sudah mencapai 100%. Tidak ada tahapan tersisa.
                            </div>
                        @else
                            <div class="flex flex-wrap gap-3 rounded-xl transition-all" :class="{ 'outline outline-2 outline-red-400 p-3': attempted && !persentaseSelected }">
                                @foreach($allowedOptions as $option)
                                    <label class="relative flex cursor-pointer">
                                        <input class="sr-only peer" name="persentase" type="radio" value="{{ $option }}"
                                            @checked(old('persentase', $draft?->persentase) == $option)
                                            @change="persentaseSelected = true">
                                        <div class="min-w-[80px] text-center px-6 py-4 rounded-xl border-2 border-slate-200 text-on-surface-variant font-bold text-lg transition-all
                                            peer-checked:border-[#F9D423] peer-checked:bg-[#F9D423]/10 peer-checked:text-[#0F4C81] hover:border-[#F9D423]/50">
                                            {{ $option }}%
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            <p x-show="attempted && !persentaseSelected" x-cloak class="text-sm text-red-500 flex items-center gap-1 mt-1">
                                <span class="material-symbols-outlined text-base">warning</span>
                                Pilih salah satu persentase penyelesaian.
                            </p>
                        @endif

                        @error('persentase')
                            <p class="text-sm text-error mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div class="space-y-2">
                        <label for="deskripsi" class="block text-xs font-bold text-[#0F4C81] uppercase tracking-widest">
                            Keterangan Update <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            id="deskripsi"
                            name="deskripsi"
                            rows="4"
                            x-model="deskripsi"
                            class="w-full bg-surface-container-low border-none rounded-xl p-4 text-on-surface placeholder:text-on-surface-variant/50 transition-all"
                            :class="{ 'ring-2 ring-red-400': attempted && !deskripsi.trim(), 'focus:ring-2 focus:ring-[#F9D423]': !(attempted && !deskripsi.trim()) }"
                            placeholder="Deskripsikan perkembangan di lapangan..."
                        ></textarea>
                        <p x-show="attempted && !deskripsi.trim()" x-cloak class="text-sm text-red-500 flex items-center gap-1">
                            <span class="material-symbols-outlined text-base">warning</span>
                            Keterangan update wajib diisi.
                        </p>
                        @error('deskripsi')
                            <p class="text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Foto --}}
                    <div class="space-y-3">
                        <label for="fotos" class="block text-xs font-bold text-[#0F4C81] uppercase tracking-widest">
                            Upload Foto Lapangan <span class="text-red-500">*</span> <span class="text-[10px] font-normal normal-case text-on-surface-variant">(Maks 5)</span>
                        </label>
                        <div class="grid grid-cols-4 gap-4 rounded-xl transition-all" :class="{ 'outline outline-2 outline-red-400 p-2': attempted && !fotoOk }">
                            <label for="fotos" class="aspect-square rounded-xl border-2 border-dashed border-outline-variant flex flex-col items-center justify-center text-on-surface-variant hover:bg-surface-container transition-colors cursor-pointer group">
                                <span class="material-symbols-outlined text-3xl mb-1 group-hover:scale-110 transition-transform">add_a_photo</span>
                                <span class="text-[10px] font-bold" x-text="newPreviews.length > 0 ? newPreviews.length + ' foto' : 'TAMBAH'"></span>
                            </label>
                            <template x-for="(url, idx) in displayPhotos" :key="idx">
                                <div class="relative group aspect-square rounded-xl overflow-hidden ring-2 ring-[#F9D423]/40">
                                    <img class="w-full h-full object-cover" :src="url" alt="Foto preview">
                                    <div x-show="newPreviews.length > 0" class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors"></div>
                                </div>
                            </template>
                            <template x-for="i in emptySlotList" :key="'e'+i">
                                <div class="aspect-square rounded-xl bg-surface-container-low border border-outline-variant/30 flex items-center justify-center italic text-[10px] text-on-surface-variant">Kosong</div>
                            </template>
                        </div>
                        <input id="fotos" name="fotos[]" type="file" multiple accept="image/*" class="sr-only"
                            @change="handleFotoChange($event)">
                        <p x-show="attempted && !fotoOk" x-cloak class="text-sm text-red-500 flex items-center gap-1">
                            <span class="material-symbols-outlined text-base">warning</span>
                            Minimal 1 foto lapangan wajib diunggah.
                        </p>
                        @error('fotos')
                            <p class="text-sm text-error">{{ $message }}</p>
                        @enderror
                        @error('fotos.*')
                            <p class="text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status Instalasi --}}
                    <div class="space-y-3">
                        <label class="block text-xs font-bold text-[#0F4C81] uppercase tracking-widest">
                            Status Instalasi <span class="text-red-500">*</span>
                        </label>
                        <div class="flex flex-wrap gap-4 rounded-xl transition-all" :class="{ 'outline outline-2 outline-red-400 p-3': attempted && !statusSelected }">
                            <label class="relative flex cursor-pointer group">
                                <input class="sr-only peer" name="status_progress" type="radio" value="dijadwalkan"
                                    @checked($currentStatus === 'dijadwalkan')
                                    @change="statusSelected = true">
                                <div class="px-6 py-3 rounded-xl border-2 border-slate-200 text-on-surface-variant font-bold text-sm transition-all
                                    peer-checked:border-[#F9D423] peer-checked:bg-[#F9D423]/10 peer-checked:text-[#0F4C81] hover:border-[#F9D423]/50">
                                    Dijadwalkan
                                </div>
                            </label>
                            <label class="relative flex cursor-pointer group">
                                <input class="sr-only peer" name="status_progress" type="radio" value="berjalan"
                                    @checked($currentStatus === 'berjalan')
                                    @change="statusSelected = true">
                                <div class="px-6 py-3 rounded-xl border-2 border-slate-200 text-on-surface-variant font-bold text-sm transition-all
                                    peer-checked:border-[#F9D423] peer-checked:bg-[#F9D423]/10 peer-checked:text-[#0F4C81] hover:border-[#F9D423]/50">
                                    Berjalan
                                </div>
                            </label>
                            <label class="relative flex cursor-pointer group">
                                <input class="sr-only peer" name="status_progress" type="radio" value="selesai"
                                    @checked($currentStatus === 'selesai')
                                    @change="statusSelected = true">
                                <div class="px-6 py-3 rounded-xl border-2 border-slate-200 text-on-surface-variant font-bold text-sm transition-all
                                    peer-checked:border-[#27AE60] peer-checked:bg-[#27AE60]/10 peer-checked:text-[#27AE60] hover:border-[#27AE60]/50">
                                    Selesai
                                </div>
                            </label>
                        </div>
                        <p x-show="attempted && !statusSelected" x-cloak class="text-sm text-red-500 flex items-center gap-1">
                            <span class="material-symbols-outlined text-base">warning</span>
                            Status instalasi wajib dipilih.
                        </p>
                        @error('status_progress')
                            <p class="text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-6 border-t border-slate-100">
                        <button
                            class="px-8 py-3 rounded-xl font-bold text-on-surface-variant hover:bg-surface-container transition-all active:scale-95 duration-200"
                            type="submit"
                            name="save_draft"
                            value="1"
                        >
                            Simpan Draft
                        </button>
                        <button
                            class="bg-[#F9D423] hover:bg-[#e8c404] text-[#0F4C81] px-10 py-3 rounded-xl font-bold shadow-lg shadow-[#F9D423]/20 transition-all active:scale-95 duration-200"
                            type="button"
                            @click="trySubmit()"
                        >
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
