@extends('layouts.admin')

@section('content')

@if(session('success'))
<div class="bg-sustainability-green/10 text-sustainability-green border border-sustainability-green/20 p-4 rounded-lg mb-8 max-w-[1000px] mx-auto flex items-center gap-3">
    <span class="material-symbols-outlined">check_circle</span>
    <span class="font-bold">{{ session('success') }}</span>
</div>
@endif

<div class="max-w-[1000px] mx-auto">
    <div class="bg-surface-container-lowest rounded-xl shadow-sm border border-slate-100 overflow-hidden mb-8">
        <div class="p-8 border-b border-surface-container-low flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-extrabold text-deep-navy font-headline mb-2">{{ $proyek->judul }}</h1>
                <p class="text-on-surface-variant flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">location_on</span>
                    {{ $proyek->desa->nama }}, {{ $proyek->desa->provinsi }}
                </p>
            </div>
            <div class="text-right">
                <span class="bg-sustainability-green text-white text-[10px] font-bold px-3 py-1.5 rounded-full inline-block mb-2">{{ strtoupper(str_replace('_', ' ', $proyek->status)) }}</span>
                <p class="text-xs text-on-surface-variant">Dibuat pada {{ $proyek->created_at->format('d M Y') }}</p>
            </div>
        </div>
        
        <div class="p-8 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="md:col-span-2 space-y-8">
                <section>
                    <h3 class="text-lg font-bold text-deep-navy border-b-2 border-solar-gold inline-block pb-1 mb-4">Deskripsi</h3>
                    <p class="text-on-surface-variant leading-relaxed">{{ $proyek->deskripsi ?: 'Belum ada deskripsi.' }}</p>
                </section>
                
                @if($proyek->fotos->count() > 0)
                <section>
                    <h3 class="text-lg font-bold text-deep-navy border-b-2 border-solar-gold inline-block pb-1 mb-4">Galeri Dokumentasi</h3>
                    <div class="flex gap-4 overflow-x-auto pb-4 no-scrollbar">
                        @foreach($proyek->fotos as $foto)
                        <img src="{{ Storage::url($foto->path) }}" class="h-32 rounded-lg shrink-0 border border-surface-container-low" alt="Proyek foto">
                        @endforeach
                    </div>
                </section>
                @endif
            </div>
            
            <div class="space-y-6">
                <div class="bg-surface p-6 rounded-xl border border-surface-container-high/50">
                    <h3 class="font-bold text-deep-navy mb-4">Informasi Operasional</h3>
                    <ul class="space-y-4">
                        <li>
                            <p class="text-[10px] uppercase font-bold text-on-surface-variant">Jenis Energi</p>
                            <p class="font-bold text-deep-navy">{{ ucfirst(str_replace('_', ' ', $proyek->jenis_energi)) }}</p>
                        </li>
                        <li>
                            <p class="text-[10px] uppercase font-bold text-on-surface-variant">Penyedia Energi Terpilih</p>
                            <p class="font-bold text-deep-navy">{{ $proyek->penyedia ? $proyek->penyedia->nama : 'Belum dipilih' }}</p>
                        </li>
                        <li>
                            <p class="text-[10px] uppercase font-bold text-on-surface-variant">Estimasi Mulai</p>
                            <p class="font-medium text-deep-navy">{{ $proyek->estimasi_mulai ? $proyek->estimasi_mulai->format('d M Y') : '-' }}</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection