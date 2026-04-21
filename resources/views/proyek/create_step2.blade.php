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
            <div class="w-10 h-10 rounded-full bg-solar-gold text-deep-navy flex items-center justify-center font-bold ring-4 ring-surface">2</div>
            <span class="text-sm font-bold text-on-surface">Pilih Penyedia</span>
        </div>
        <div class="flex flex-col items-center gap-3 bg-surface">
            <div class="w-10 h-10 rounded-full bg-surface-container-highest text-on-surface-variant flex items-center justify-center font-bold ring-4 ring-surface">3</div>
            <span class="text-sm font-medium text-on-surface-variant">Review & Simpan</span>
        </div>
    </div>
</div>

<form action="{{ route('proyek.save.step2', $proyek->id) }}" method="POST" class="max-w-[1000px] mx-auto">
    @csrf

    <div class="mb-8 flex justify-between items-end">
        <div>
            <h2 class="text-3xl font-extrabold text-deep-navy font-headline">Rekomendasi Penyedia Energi</h2>
            <p class="text-on-surface-variant mt-2">Daftar penyedia terbaik yang sesuai dengan profil Desa Terang Baru berdasarkan AI.</p>
        </div>
        
        <div class="flex gap-3">
            <select class="bg-white border-surface-container-highest rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-solar-gold outline-none">
                <option>Semua Kategori</option>
                <option>Solar Panel</option>
                <option>Micro-Hydro</option>
            </select>
        </div>
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

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        @foreach($recommendations as $index => $penyedia)
        <label class="relative block cursor-pointer group">
            <input type="radio" name="penyedia_id" value="{{ $penyedia->id }}" class="peer sr-only" {{ old('penyedia_id', $proyek->penyedia_id) == $penyedia->id ? 'checked' : '' }}>
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
                            {{ ucfirst($penyedia->spesialisasi) }} Spesialis
                        </p>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Total Skor AI</p>
                                <div class="flex items-end gap-1">
                                    <span class="text-xl font-black text-sustainability-green leading-none">{{ $penyedia->match_score }}</span><span class="text-xs font-medium text-on-surface-variant pb-0.5">/100</span>
                                </div>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Estimasi Budget</p>
                                <p class="font-bold text-deep-navy text-sm">Rp {{ number_format($penyedia->kisaran_harga_min, 0, ',', '.') }} - {{ number_format($penyedia->kisaran_harga_max, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </label>
        @endforeach
    </div>

    <!-- Actions -->
    <div class="flex justify-between items-center pt-6 border-t border-surface-container relative z-10">
        <a href="{{ route('proyek.create', ['draft_id' => $proyek->id]) }}" class="px-8 py-4 rounded-lg font-bold text-on-surface-variant hover:bg-surface-container-low transition-colors">
            Kembali
        </a>
        <div class="flex gap-4">
            <button type="submit" class="bg-solar-gold px-8 py-4 rounded-lg text-deep-navy font-bold shadow-lg hover:brightness-105 active:scale-[0.98] transition-all flex items-center gap-2">
                Simpan & Review
                <span class="material-symbols-outlined">arrow_forward</span>
            </button>
        </div>
    </div>
</form>
@endsection