@extends('layouts.admin')

@section('content')
<div class="max-w-[800px] mx-auto mb-12">
    <div class="flex items-center justify-between relative">
        <div class="absolute top-1/2 left-0 w-full h-1 bg-surface-container -translate-y-1/2 -z-10"></div>
        <div class="flex flex-col items-center gap-3 bg-surface">
            <div class="w-10 h-10 rounded-full bg-sustainability-green text-white flex items-center justify-center font-bold ring-4 ring-surface shadow-sm"><span class="material-symbols-outlined text-sm">check</span></div>
            <span class="text-sm font-bold text-on-surface">Detail Proyek</span>
        </div>
        <div class="flex flex-col items-center gap-3 bg-surface">
            <div class="w-10 h-10 rounded-full bg-sustainability-green text-white flex items-center justify-center font-bold ring-4 ring-surface shadow-sm"><span class="material-symbols-outlined text-sm">check</span></div>
            <span class="text-sm font-bold text-on-surface">Pilih Penyedia</span>
        </div>
        <div class="flex flex-col items-center gap-3 bg-surface">
            <div class="w-10 h-10 rounded-full bg-solar-gold text-deep-navy flex items-center justify-center font-bold ring-4 ring-surface">3</div>
            <span class="text-sm font-bold text-on-surface">Review & Simpan</span>
        </div>
    </div>
</div>

<div class="max-w-[800px] mx-auto">
    <div class="bg-surface-container-lowest rounded-xl shadow-[0_32px_64px_-12px_rgba(24,28,28,0.06)] overflow-hidden border border-slate-100">
        <div class="p-8 border-b border-surface-container-low flex justify-between items-center bg-surface w-full">
            <div>
                <h2 class="text-2xl font-extrabold text-deep-navy font-headline mb-2">Review Proyek</h2>
                <p class="text-on-surface-variant leading-relaxed">Periksa kembali detail proyek sebelum mengirimkan ke pihak penyedia energi.</p>
            </div>
            <span class="bg-primary/10 text-primary text-xs font-bold px-3 py-1.5 rounded-full">STATUS: {{ strtoupper($proyek->status) }}</span>
        </div>
        
        <div class="p-8 space-y-8">
            <!-- Informasi Proyek -->
            <div>
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-deep-navy border-b-2 border-solar-gold inline-block pb-1">Informasi Dasar</h3>
                    <a href="{{ route('proyek.create', ['draft_id' => $proyek->id]) }}" class="text-primary text-sm font-medium hover:underline flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">edit</span> Edit
                    </a>
                </div>
                <div class="bg-surface p-6 rounded-xl space-y-4">
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <p class="text-[10px] uppercase font-bold text-on-surface-variant mb-1">Judul Proyek</p>
                            <p class="font-bold text-on-surface">{{ $proyek->judul }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase font-bold text-on-surface-variant mb-1">Desa Target</p>
                            <p class="font-bold text-on-surface">{{ $proyek->desa->nama }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase font-bold text-on-surface-variant mb-1">Jenis Energi</p>
                            <p class="font-bold text-on-surface">{{ ucfirst($proyek->jenis_energi) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Penyedia -->
            @if($proyek->penyedia)
            <div>
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-deep-navy border-b-2 border-solar-gold inline-block pb-1">Penyedia Terpilih</h3>
                    <a href="{{ route('proyek.step2', $proyek->id) }}" class="text-primary text-sm font-medium hover:underline flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">edit</span> Edit
                    </a>
                </div>
                <div class="bg-primary-container/10 p-6 rounded-xl border border-primary-container/20 flex gap-4 items-center">
                    <div class="w-12 h-12 rounded-full bg-surface-container-low flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-2xl text-primary" style="font-variation-settings: 'FILL' 1;">solar_power</span>
                    </div>
                    <div>
                        <h4 class="font-bold text-deep-navy text-lg">{{ $proyek->penyedia->nama }}</h4>
                        <p class="text-sm text-on-surface-variant">{{ ucfirst($proyek->penyedia->spesialisasi) }} | Rp {{ number_format($proyek->penyedia->kisaran_harga_min,0,',','.') }} - Rp {{ number_format($proyek->penyedia->kisaran_harga_max,0,',','.') }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
        
        <div class="p-8 bg-surface-container-low/50">
            <form action="{{ route('proyek.kirim', $proyek->id) }}" method="POST" class="flex gap-4">
                @csrf
                <a href="{{ route('proyek.step2', $proyek->id) }}" class="px-8 py-4 rounded-lg font-bold text-on-surface-variant hover:bg-surface-container-low transition-colors">Kembali</a>
                <button type="submit" class="flex-1 bg-sustainability-green py-5 rounded-lg text-white font-bold text-lg shadow-lg hover:brightness-105 active:scale-[0.98] transition-all flex items-center justify-center gap-3">
                    <span class="material-symbols-outlined">send</span> Publikasi & Kirim ke Penyedia
                </button>
            </form>
        </div>
    </div>
</div>
@endsection