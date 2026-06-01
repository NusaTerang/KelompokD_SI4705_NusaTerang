@extends('layouts.admin')

@section('breadcrumbs')
    <span>Dashboard</span>
    <span class="mx-1 text-slate-400">/</span>
    <span>Proyek Energi</span>
    <span class="mx-1 text-slate-400">/</span>
    <span class="font-medium text-slate-700">{{ $proyek ? 'Edit Proyek' : 'Buat Proyek' }} — Step 1</span>
@endsection

@section('page_heading', $proyek ? 'Edit Proyek' : 'Buat Proyek Baru')

@section('content')
    <div class="max-w-[800px] mx-auto mb-12">
        <div class="flex items-center justify-between relative">
            <div class="absolute top-1/2 left-0 w-full h-1 bg-surface-container -translate-y-1/2 -z-10"></div>
            <div class="flex flex-col items-center gap-3 bg-surface">
                <div class="w-10 h-10 rounded-full bg-solar-gold text-deep-navy flex items-center justify-center font-bold ring-4 ring-surface shadow-sm">
                    1
                </div>
                <span class="text-sm font-bold text-on-surface">Detail Proyek</span>
            </div>
            <div class="flex flex-col items-center gap-3 bg-surface">
                <div class="w-10 h-10 rounded-full bg-surface-container-highest text-on-surface-variant flex items-center justify-center font-bold ring-4 ring-surface">
                    2
                </div>
                <span class="text-sm font-medium text-on-surface-variant">Pilih Penyedia</span>
            </div>
            <div class="flex flex-col items-center gap-3 bg-surface">
                <div class="w-10 h-10 rounded-full bg-surface-container-highest text-on-surface-variant flex items-center justify-center font-bold ring-4 ring-surface">
                    3
                </div>
                <span class="text-sm font-medium text-on-surface-variant">Review & Simpan</span>
            </div>
        </div>
    </div>

    <form action="{{ route('proyek.save.step1') }}" method="POST" enctype="multipart/form-data" class="max-w-[800px] mx-auto">
        @csrf
        @if($proyek)
            <input type="hidden" name="draft_id" value="{{ $proyek->id }}">
        @endif

        <div class="bg-surface-container-lowest rounded-xl shadow-[0_32px_64px_-12px_rgba(24,28,28,0.06)] overflow-hidden border border-slate-100">
            <div class="p-8 border-b border-surface-container-low">
                <h2 class="text-2xl font-extrabold text-deep-navy font-headline mb-2">Informasi Dasar Proyek</h2>
                <p class="text-on-surface-variant leading-relaxed">Berikan detail lengkap mengenai lokasi dan tujuan proyek energi terbarukan Anda.</p>
            </div>

            <div class="p-8 space-y-8">

                @if ($errors->any())
                    <div class="p-4 bg-red-100 text-red-700 rounded-lg">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="space-y-3">
                    <label class="text-xs font-bold text-deep-navy uppercase tracking-widest">Pilih Desa Target <span class="text-red-600">*</span></label>
                    <select name="desa_id" class="w-full bg-surface-container-low rounded-lg px-4 py-4 focus:ring-2 focus:ring-solar-gold transition-all text-on-surface {{ $errors->has('desa_id') ? 'border border-red-500' : 'border-0' }}">
                        <option value="">Pilih Desa</option>
                        @foreach($desas as $desa)
                            <option value="{{ $desa->id_desa }}" {{ (old('desa_id', $proyek->desa_id ?? request('desa_id')) == $desa->id_desa) ? 'selected' : '' }}>
                                {{ $desa->nama_desa }} - {{ $desa->provinsi }}
                            </option>
                        @endforeach
                    </select>
                    @error('desa_id')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-3">
                    <label class="text-xs font-bold text-deep-navy uppercase tracking-widest">Judul Proyek <span class="text-red-600">*</span></label>
                    <input name="judul" value="{{ old('judul', $proyek->judul ?? '') }}" required
                        class="w-full bg-surface-container-low rounded-lg px-4 py-4 focus:ring-2 focus:ring-solar-gold transition-all text-on-surface {{ $errors->has('judul') ? 'border border-red-500' : 'border-0' }}"
                        placeholder="Contoh: Instalasi Panel Surya Desa X" type="text" />
                    @error('judul')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-3">
                    <label class="text-xs font-bold text-deep-navy uppercase tracking-widest">Jenis Energi <span class="text-red-600">*</span></label>
                    <select name="jenis_energi" required class="w-full bg-surface-container-low rounded-lg px-4 py-4 focus:ring-2 focus:ring-solar-gold transition-all text-on-surface {{ $errors->has('jenis_energi') ? 'border border-red-500' : 'border-0' }}">
                        <option value="panel_surya"          {{ old('jenis_energi', $proyek->jenis_energi ?? '') == 'panel_surya'          ? 'selected' : '' }}>Panel Surya</option>
                        <option value="mikro_hidro"          {{ old('jenis_energi', $proyek->jenis_energi ?? '') == 'mikro_hidro'          ? 'selected' : '' }}>Mikro Hidro</option>
                        <option value="biogas"               {{ old('jenis_energi', $proyek->jenis_energi ?? '') == 'biogas'               ? 'selected' : '' }}>Biogas</option>
                        <option value="hybrid_solar_baterai" {{ old('jenis_energi', $proyek->jenis_energi ?? '') == 'hybrid_solar_baterai' ? 'selected' : '' }}>Hybrid Solar + Baterai</option>
                    </select>
                    @error('jenis_energi')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-3">
                    <label class="text-xs font-bold text-deep-navy uppercase tracking-widest">Deskripsi Proyek <span class="text-red-600">*</span></label>
                    <textarea name="deskripsi" required
                        class="w-full bg-surface-container-low rounded-lg px-4 py-4 focus:ring-2 focus:ring-solar-gold transition-all text-on-surface resize-none {{ $errors->has('deskripsi') ? 'border border-red-500' : 'border-0' }}"
                        placeholder="Ceritakan tujuan proyek..."
                        rows="6">{{ old('deskripsi', $proyek->deskripsi ?? '') }}</textarea>
                    @error('deskripsi')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-3">
                    <label class="text-xs font-bold text-deep-navy uppercase tracking-widest">Upload Foto Proyek (Maks 5) <span class="text-red-600">*</span></label>
                    <input type="file" name="fotos[]" multiple accept="image/*"
                        class="w-full bg-surface-container-low rounded-lg px-4 py-4 focus:ring-2 focus:ring-solar-gold transition-all text-on-surface {{ $errors->has('fotos') || $errors->has('fotos.*') ? 'border border-red-500' : 'border-0' }}">
                    @error('fotos')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('fotos.*')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @if($proyek && $proyek->fotos->count() > 0)
                        <p class="text-sm font-bold mt-2">Foto Terunggah:</p>
                        <div class="flex gap-2 flex-wrap">
                            @foreach($proyek->fotos as $foto)
                                <img src="{{ str_starts_with($foto->path, 'http') ? $foto->path : asset('storage/' . $foto->path) }}" class="w-16 h-16 object-cover rounded shadow">
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <label class="text-xs font-bold text-deep-navy uppercase tracking-widest">Tanggal Mulai <span class="text-red-600">*</span></label>
                        <input name="estimasi_mulai" required
                            value="{{ old('estimasi_mulai', ($proyek && $proyek->estimasi_mulai) ? $proyek->estimasi_mulai->format('Y-m-d') : '') }}"
                            class="w-full bg-surface-container-low rounded-lg px-4 py-4 focus:ring-2 focus:ring-solar-gold transition-all text-on-surface {{ $errors->has('estimasi_mulai') ? 'border border-red-500' : 'border-0' }}"
                            type="date" />
                        @error('estimasi_mulai')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-3">
                        <label class="text-xs font-bold text-deep-navy uppercase tracking-widest">Tanggal Berakhir <span class="text-red-600">*</span></label>
                        <input name="estimasi_selesai" required
                            value="{{ old('estimasi_selesai', ($proyek && $proyek->estimasi_selesai) ? $proyek->estimasi_selesai->format('Y-m-d') : '') }}"
                            class="w-full bg-surface-container-low rounded-lg px-4 py-4 focus:ring-2 focus:ring-solar-gold transition-all text-on-surface {{ $errors->has('estimasi_selesai') ? 'border border-red-500' : 'border-0' }}"
                            type="date" />
                        @error('estimasi_selesai')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

            </div>

            <div class="p-8 bg-surface-container-low/50">
                <button type="submit"
                    class="w-full bg-solar-gold py-5 rounded-lg text-deep-navy font-bold text-lg shadow-lg hover:brightness-105 active:scale-[0.98] transition-all flex items-center justify-center gap-3">
                    Lanjut Pilih Penyedia
                    <span class="material-symbols-outlined">arrow_forward</span>
                </button>
            </div>
        </div>
    </form>
@endsection
