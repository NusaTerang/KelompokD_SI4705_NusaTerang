@extends('layouts.admin')

@section('title', 'Tambah Penyedia Energi')
@section('page_heading', 'Tambah Penyedia Energi Baru')

@section('breadcrumbs')
    <span>Dashboard</span>
    <span class="mx-1">›</span>
    <a href="{{ route('admin.vendors.index') }}" class="hover:text-nt-navy">Penyedia Energi</a>
    <span class="mx-1">›</span>
    <span class="font-semibold text-nt-navy">Tambah</span>
@endsection

@section('content')

<div class="max-w-3xl w-full mx-auto">

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-8">
        <form action="{{ route('admin.vendors.store') }}" method="POST" class="space-y-8 font-body">
            @csrf

            {{-- Nama Perusahaan --}}
            <div>
                <label for="nama" class="block text-sm font-semibold text-on-surface mb-2">Nama Perusahaan</label>
                <input type="text" id="nama" name="nama" value="{{ old('nama') }}"
                       placeholder="PT Energi Nusantara Berkembang"
                       class="w-full bg-surface-container-highest border border-slate-200 rounded-lg px-4 py-3 text-sm text-on-surface focus:border-secondary focus:ring-1 focus:ring-secondary outline-none transition-all @error('nama') border-error @enderror" />
                @error('nama') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            {{-- Spesialisasi & Kontak --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="spesialisasi" class="block text-sm font-semibold text-on-surface mb-2">Spesialisasi</label>
                    <div class="relative">
                        <select id="spesialisasi" name="spesialisasi"
                                class="w-full appearance-none bg-surface-container-highest border border-slate-200 rounded-lg px-4 py-3 text-sm text-on-surface focus:border-secondary focus:ring-1 focus:ring-secondary outline-none transition-all cursor-pointer @error('spesialisasi') border-error @enderror">
                            <option value="" disabled selected>Pilih spesialisasi...</option>
                            <option value="solar"       {{ old('spesialisasi') === 'solar'       ? 'selected' : '' }}>Solar PV (Surya)</option>
                            <option value="mikro_hidro" {{ old('spesialisasi') === 'mikro_hidro' ? 'selected' : '' }}>Tenaga Air (Mikrohidro)</option>
                            <option value="lainnya"     {{ old('spesialisasi') === 'lainnya'     ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[18px]">expand_more</span>
                    </div>
                    @error('spesialisasi') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="no_telepon" class="block text-sm font-semibold text-on-surface mb-2">Nomor Kontak (Telp/WA)</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">call</span>
                        <input type="tel" id="no_telepon" name="no_telepon" value="{{ old('no_telepon') }}"
                               placeholder="+62 812 3456 7890"
                               class="w-full bg-surface-container-highest border border-slate-200 rounded-lg pl-10 pr-4 py-3 text-sm text-on-surface focus:border-secondary focus:ring-1 focus:ring-secondary outline-none transition-all" />
                    </div>
                </div>
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-semibold text-on-surface mb-2">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                       placeholder="vendor@example.com"
                       class="w-full bg-surface-container-highest border border-slate-200 rounded-lg px-4 py-3 text-sm text-on-surface focus:border-secondary focus:ring-1 focus:ring-secondary outline-none transition-all @error('email') border-error @enderror" />
                @error('email') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
            </div>

            {{-- Provinsi & Kota --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2 border-t border-slate-100">
                <div>
                    <label for="provinsi_operasi" class="block text-sm font-semibold text-on-surface mb-2">Provinsi</label>
                    <input type="text" id="provinsi_operasi" name="provinsi_operasi" value="{{ old('provinsi_operasi') }}"
                           placeholder="Contoh: Jawa Barat"
                           class="w-full bg-surface-container-highest border border-slate-200 rounded-lg px-4 py-3 text-sm text-on-surface focus:border-secondary focus:ring-1 focus:ring-secondary outline-none transition-all" />
                </div>
                <div>
                    <label for="kota" class="block text-sm font-semibold text-on-surface mb-2">Kota / Kabupaten</label>
                    <input type="text" id="kota" name="kota" value="{{ old('kota') }}"
                           placeholder="Contoh: Bandung"
                           class="w-full bg-surface-container-highest border border-slate-200 rounded-lg px-4 py-3 text-sm text-on-surface focus:border-secondary focus:ring-1 focus:ring-secondary outline-none transition-all" />
                </div>
            </div>

            {{-- Koordinat --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="latitude" class="block text-sm font-semibold text-on-surface mb-2">Koordinat Latitude</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">location_on</span>
                        <input type="text" id="latitude" name="latitude" value="{{ old('latitude') }}"
                               placeholder="-6.917464"
                               class="w-full bg-surface-container-highest border border-slate-200 rounded-lg pl-10 pr-4 py-3 text-sm text-on-surface focus:border-secondary focus:ring-1 focus:ring-secondary outline-none transition-all @error('latitude') border-error @enderror" />
                    </div>
                    @error('latitude') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="longitude" class="block text-sm font-semibold text-on-surface mb-2">Koordinat Longitude</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">location_on</span>
                        <input type="text" id="longitude" name="longitude" value="{{ old('longitude') }}"
                               placeholder="107.619123"
                               class="w-full bg-surface-container-highest border border-slate-200 rounded-lg pl-10 pr-4 py-3 text-sm text-on-surface focus:border-secondary focus:ring-1 focus:ring-secondary outline-none transition-all @error('longitude') border-error @enderror" />
                    </div>
                    @error('longitude') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Kapasitas & Harga --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2 border-t border-slate-100">
                <div>
                    <label for="kapasitas_maks" class="block text-sm font-semibold text-on-surface mb-2">Kapasitas Maksimal Daya</label>
                    <div class="flex items-center">
                        <input type="number" id="kapasitas_maks" name="kapasitas_maks" value="{{ old('kapasitas_maks') }}"
                               placeholder="500" min="0"
                               class="w-full bg-surface-container-highest border border-slate-200 rounded-l-lg px-4 py-3 text-sm text-on-surface focus:border-secondary focus:ring-1 focus:ring-secondary outline-none transition-all border-r-0 @error('kapasitas_maks') border-error @enderror" />
                        <span class="px-4 py-3 rounded-r-lg border border-slate-200 border-l-0 bg-surface-container-low text-slate-500 text-sm whitespace-nowrap">kWh</span>
                    </div>
                    @error('kapasitas_maks') <p class="mt-1 text-xs text-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-on-surface mb-2">Rentang Harga Layanan (IDR/kWh)</label>
                    <div class="flex items-center gap-2">
                        <input type="number" id="kisaran_harga_min" name="kisaran_harga_min" value="{{ old('kisaran_harga_min') }}"
                               placeholder="Min" min="0"
                               class="w-full bg-surface-container-highest border border-slate-200 rounded-lg px-4 py-3 text-sm text-on-surface focus:border-secondary focus:ring-1 focus:ring-secondary outline-none transition-all" />
                        <span class="text-slate-400 font-medium">–</span>
                        <input type="number" id="kisaran_harga_max" name="kisaran_harga_max" value="{{ old('kisaran_harga_max') }}"
                               placeholder="Max" min="0"
                               class="w-full bg-surface-container-highest border border-slate-200 rounded-lg px-4 py-3 text-sm text-on-surface focus:border-secondary focus:ring-1 focus:ring-secondary outline-none transition-all" />
                    </div>
                </div>
            </div>

            {{-- Deskripsi --}}
            <div class="pt-2 border-t border-slate-100">
                <label for="deskripsi" class="block text-sm font-semibold text-on-surface mb-2">Deskripsi Singkat Perusahaan</label>
                <textarea id="deskripsi" name="deskripsi" rows="4"
                          placeholder="Jelaskan secara singkat mengenai pengalaman, visi, dan keunggulan penyedia energi ini..."
                          class="w-full bg-surface-container-highest border border-slate-200 rounded-lg px-4 py-3 text-sm text-on-surface focus:border-secondary focus:ring-1 focus:ring-secondary outline-none transition-all resize-none">{{ old('deskripsi') }}</textarea>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="{{ route('admin.vendors.index') }}"
                   class="px-6 py-2.5 text-sm font-semibold text-slate-500 hover:text-on-surface hover:bg-slate-100 rounded-lg transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold bg-primary-container text-on-primary-container rounded-lg hover:brightness-105 transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1">save</span>
                    Simpan
                </button>
            </div>
        </form>
    </div>

</div>

@endsection
