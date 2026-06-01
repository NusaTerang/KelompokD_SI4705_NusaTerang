@extends('layouts.admin')

@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-deep-navy cursor-pointer">Dashboard</a>
    <span class="mx-1 text-slate-400">/</span>
    <a href="{{ route('proyek.kelola') }}" class="hover:text-deep-navy cursor-pointer">Proyek Energi</a>
    <span class="mx-1 text-slate-400">/</span>
    <span class="font-medium text-slate-700">Edit Proyek</span>
@endsection

@section('page_heading', 'Edit Proyek')

@section('content')
    <form action="{{ route('proyek.update', $proyek->id) }}" method="POST" enctype="multipart/form-data" class="max-w-[800px] mx-auto mb-12">
        @csrf
        @method('PUT')

        <div class="bg-surface-container-lowest rounded-xl shadow-[0_32px_64px_-12px_rgba(24,28,28,0.06)] overflow-hidden border border-slate-100">
            <div class="p-8 border-b border-surface-container-low">
                <h2 class="text-2xl font-extrabold text-deep-navy font-headline mb-2">Edit Informasi Proyek</h2>
                <p class="text-on-surface-variant leading-relaxed">Perbarui detail, tanggal, atau penyedia untuk proyek energi ini.</p>
            </div>

            <div class="p-8 space-y-8">

                @if ($errors->any())
                    <div class="p-4 bg-red-100 text-red-700 rounded-lg">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="space-y-3">
                    <label class="text-xs font-bold text-deep-navy uppercase tracking-widest">Pilih Desa Target <span class="text-red-600">*</span></label>
                    <select name="desa_id" required class="w-full bg-surface-container-low rounded-lg px-4 py-4 focus:ring-2 focus:ring-solar-gold transition-all text-on-surface {{ $errors->has('desa_id') ? 'border border-red-500' : 'border-0' }}">
                        <option value="">Pilih Desa</option>
                        @foreach($desas as $desa)
                            <option value="{{ $desa->id_desa }}" {{ (old('desa_id', $proyek->desa_id)) == $desa->id_desa ? 'selected' : '' }}>
                                {{ $desa->nama_desa }} - {{ $desa->provinsi }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-3">
                    <label class="text-xs font-bold text-deep-navy uppercase tracking-widest">Judul Proyek <span class="text-red-600">*</span></label>
                    <input name="judul" value="{{ old('judul', $proyek->judul) }}" required
                        class="w-full bg-surface-container-low rounded-lg px-4 py-4 focus:ring-2 focus:ring-solar-gold transition-all text-on-surface {{ $errors->has('judul') ? 'border border-red-500' : 'border-0' }}"
                        placeholder="Contoh: Instalasi Panel Surya Desa X" type="text" />
                </div>

                <div class="space-y-3">
                    <label class="text-xs font-bold text-deep-navy uppercase tracking-widest">Jenis Energi <span class="text-red-600">*</span></label>
                    <select name="jenis_energi" required class="w-full bg-surface-container-low rounded-lg px-4 py-4 focus:ring-2 focus:ring-solar-gold transition-all text-on-surface {{ $errors->has('jenis_energi') ? 'border border-red-500' : 'border-0' }}">
                        <option value="panel_surya"          {{ old('jenis_energi', $proyek->jenis_energi) == 'panel_surya'          ? 'selected' : '' }}>Panel Surya</option>
                        <option value="mikro_hidro"          {{ old('jenis_energi', $proyek->jenis_energi) == 'mikro_hidro'          ? 'selected' : '' }}>Mikro Hidro</option>
                        <option value="biogas"               {{ old('jenis_energi', $proyek->jenis_energi) == 'biogas'               ? 'selected' : '' }}>Biogas</option>
                        <option value="hybrid_solar_baterai" {{ old('jenis_energi', $proyek->jenis_energi) == 'hybrid_solar_baterai' ? 'selected' : '' }}>Hybrid Solar + Baterai</option>
                    </select>
                </div>

                <div class="space-y-3">
                    <label class="text-xs font-bold text-deep-navy uppercase tracking-widest">Deskripsi Proyek <span class="text-red-600">*</span></label>
                    <textarea name="deskripsi" required
                        class="w-full bg-surface-container-low rounded-lg px-4 py-4 focus:ring-2 focus:ring-solar-gold transition-all text-on-surface resize-none {{ $errors->has('deskripsi') ? 'border border-red-500' : 'border-0' }}"
                        rows="5">{{ old('deskripsi', $proyek->deskripsi) }}</textarea>
                </div>

                <div class="space-y-3">
                    <label class="text-xs font-bold text-deep-navy uppercase tracking-widest">Pilih Penyedia <span class="text-slate-400 font-normal ml-1">(Opsional)</span></label>
                    <select name="penyedia_id" class="w-full bg-surface-container-low rounded-lg px-4 py-4 focus:ring-2 focus:ring-solar-gold transition-all text-on-surface {{ $errors->has('penyedia_id') ? 'border border-red-500' : 'border-0' }}">
                        <option value="">-- Belum Pilih Penyedia --</option>
                        @foreach($allPenyedia as $p)
                            <option value="{{ $p->id }}" {{ (old('penyedia_id', $proyek->penyedia_id)) == $p->id ? 'selected' : '' }}>
                                {{ $p->nama }} ({{ ucfirst(str_replace('_', ' ', $p->spesialisasi)) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <label class="text-xs font-bold text-deep-navy uppercase tracking-widest">Tanggal Mulai <span class="text-red-600">*</span></label>
                        <input name="estimasi_mulai" required
                            value="{{ old('estimasi_mulai', $proyek->estimasi_mulai ? $proyek->estimasi_mulai->format('Y-m-d') : '') }}"
                            class="w-full bg-surface-container-low rounded-lg px-4 py-4 focus:ring-2 focus:ring-solar-gold transition-all text-on-surface"
                            type="date" />
                    </div>
                    <div class="space-y-3">
                        <label class="text-xs font-bold text-deep-navy uppercase tracking-widest">Tanggal Selesai <span class="text-red-600">*</span></label>
                        <input name="estimasi_selesai" required
                            value="{{ old('estimasi_selesai', $proyek->estimasi_selesai ? $proyek->estimasi_selesai->format('Y-m-d') : '') }}"
                            class="w-full bg-surface-container-low rounded-lg px-4 py-4 focus:ring-2 focus:ring-solar-gold transition-all text-on-surface"
                            type="date" />
                    </div>
                </div>

                <div class="space-y-3">
                    <label class="text-xs font-bold text-deep-navy uppercase tracking-widest">Tambah Foto Baru (Opsional)</label>
                    <input type="file" name="fotos[]" multiple accept="image/*"
                        class="w-full bg-surface-container-low rounded-lg px-4 py-4 focus:ring-2 focus:ring-solar-gold transition-all text-on-surface">
                    <p class="text-xs text-on-surface-variant">Hanya unggah jika ingin menambahkan foto. Maksimal 5 foto per proyek.</p>
                    
                    @if($proyek->fotos->count() > 0)
                        <p class="text-sm font-bold mt-4">Foto Saat Ini:</p>
                        <div class="flex gap-2 flex-wrap">
                            @foreach($proyek->fotos as $foto)
                                <img src="{{ str_starts_with($foto->path, 'http') ? $foto->path : asset('storage/' . $foto->path) }}" class="w-20 h-20 object-cover rounded-lg shadow-sm border border-slate-200">
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>

            <div class="p-8 bg-surface-container-low/50 flex items-center justify-between gap-4">
                <a href="{{ route('proyek.kelola') }}" class="px-6 py-4 font-bold text-on-surface-variant hover:bg-surface-container transition-colors rounded-lg">
                    Batal
                </a>
                <button type="submit"
                    class="bg-solar-gold px-8 py-4 rounded-lg text-deep-navy font-bold text-lg shadow-lg hover:brightness-105 active:scale-[0.98] transition-all flex items-center gap-3">
                    <span class="material-symbols-outlined">save</span>
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </form>
@endsection
