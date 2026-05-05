@extends('layouts.admin')

@section('title', 'Edit Data Desa')

@section('breadcrumbs')
    <span>Dashboard</span>
    <span class="mx-1 text-slate-400">/</span>
    <span>Data Desa</span>
    <span class="mx-1 text-slate-400">/</span>
    <span class="font-medium text-slate-700">Edit Data</span>
@endsection

@section('page_heading', 'Edit Data Desa')

@section('content')
@php
    $kondisi = $desa->kondisi_desa ?? '';
    
    $kecamatan = '';
    if (preg_match('/Kecamatan:\s*(.*)/', $kondisi, $matches)) $kecamatan = $matches[1];
    
    $kode_wilayah = '';
    if (preg_match('/Kode wilayah:\s*(.*)/', $kondisi, $matches)) $kode_wilayah = $matches[1];
    
    $jumlah_penduduk = '';
    if (preg_match('/Penduduk \(jiwa\):\s*(\d+)/', $kondisi, $matches)) $jumlah_penduduk = $matches[1];
    
    $jumlah_kk = '';
    if (preg_match('/Jumlah KK:\s*(\d+)/', $kondisi, $matches)) $jumlah_kk = $matches[1];
    
    $status_elektrifikasi = 'sebagian';
    if (preg_match('/Status elektrifikasi:\s*(\S+)/', $kondisi, $matches)) $status_elektrifikasi = $matches[1];
    elseif (str_contains($kondisi, 'belum_teraliri')) $status_elektrifikasi = 'belum_teraliri';
    elseif (str_contains($kondisi, 'sudah_teraliri')) $status_elektrifikasi = 'sudah_teraliri';
    
    $estimasi_kebutuhan_daya = '';
    if (preg_match('/Estimasi kebutuhan daya \(kW\):\s*([0-9\.]+)/', $kondisi, $matches)) $estimasi_kebutuhan_daya = $matches[1];
    
    $catatan_tambahan = '';
    if (preg_match('/Catatan tambahan:\s*(.*)/', $kondisi, $matches)) $catatan_tambahan = $matches[1];

    $kondisi_infrastruktur = preg_replace('/(Kecamatan|Kode wilayah|Penduduk \(jiwa\)|Jumlah KK|Status elektrifikasi|Estimasi kebutuhan daya \(kW\)|Catatan tambahan):.*/', '', $kondisi);
    $kondisi_infrastruktur = trim($kondisi_infrastruktur);
@endphp

    <div class="mx-auto flex max-w-7xl flex-col gap-6 lg:flex-row lg:items-start">
        <div class="min-w-0 flex-1 space-y-6">
            <form action="{{ route('desa.update', ['id' => $desa->id_desa]) }}" method="post" class="space-y-8">
                @csrf
                @method('PUT')

                {{-- Section A: Lokasi --}}
                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="mb-5 flex items-center gap-2 border-b border-slate-100 pb-4">
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-sky-100 text-sky-700">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                        </span>
                        <h2 class="text-base font-semibold text-nt-navy">Lokasi Desa</h2>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label for="nama_desa" class="mb-1.5 block text-sm font-medium text-slate-700">Nama Desa</label>
                            <input
                                type="text"
                                name="nama_desa"
                                id="nama_desa"
                                value="{{ old('nama_desa', $desa->nama_desa) }}"
                                placeholder="Contoh: Desa Karangrejo"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm shadow-sm placeholder:text-slate-400 focus:border-nt-navy focus:outline-none focus:ring-2 focus:ring-nt-navy/20"
                            />
                            @error('nama_desa')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="provinsi" class="mb-1.5 block text-sm font-medium text-slate-700">Provinsi</label>
                                <input 
                                    type="text" 
                                    name="provinsi" 
                                    id="provinsi" 
                                    value="{{ old('provinsi', $desa->provinsi) }}" 
                                    placeholder="Masukkan nama provinsi" 
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm shadow-sm focus:border-nt-navy focus:outline-none focus:ring-2 focus:ring-nt-navy/20"
                                />
                                @error('provinsi')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="kabupaten" class="mb-1.5 block text-sm font-medium text-slate-700">Kabupaten</label>
                                <input 
                                    type="text" 
                                    name="kabupaten" 
                                    id="kabupaten" 
                                    value="{{ old('kabupaten', $desa->kabupaten) }}" 
                                    placeholder="Masukkan nama kabupaten" 
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm shadow-sm focus:border-nt-navy focus:outline-none focus:ring-2 focus:ring-nt-navy/20"
                                />
                                @error('kabupaten')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="kecamatan" class="mb-1.5 block text-sm font-medium text-slate-700">Kecamatan</label>
                                <input type="text" name="kecamatan" id="kecamatan" value="{{ old('kecamatan', $kecamatan) }}" placeholder="Opsional" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm shadow-sm focus:border-nt-navy focus:outline-none focus:ring-2 focus:ring-nt-navy/20" />
                            </div>
                            <div>
                                <label for="kode_wilayah" class="mb-1.5 block text-sm font-medium text-slate-700">Kode Wilayah</label>
                                <input type="text" name="kode_wilayah" id="kode_wilayah" value="{{ old('kode_wilayah', $kode_wilayah) }}" placeholder="Opsional" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm shadow-sm focus:border-nt-navy focus:outline-none focus:ring-2 focus:ring-nt-navy/20" />
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                            <div class="min-w-0 flex-1">
                                <label for="koordinat" class="mb-1.5 block text-sm font-medium text-slate-700">Koordinat GPS</label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                                    </span>
                                    <input
                                        type="text"
                                        name="koordinat"
                                        id="koordinat"
                                        value="{{ old('koordinat', $desa->koordinat) }}"
                                        placeholder="-8.1234, 115.5678"
                                        class="w-full rounded-lg border border-slate-300 py-2.5 pl-10 pr-3 text-sm shadow-sm focus:border-nt-navy focus:outline-none focus:ring-2 focus:ring-nt-navy/20"
                                    />
                                </div>
                                @error('koordinat')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Section B: Kondisi --}}
                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="mb-5 flex items-center gap-2 border-b border-slate-100 pb-4">
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-sky-100 text-sky-700">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12M9 3v2.25m0 4.5V21" /></svg>
                        </span>
                        <h2 class="text-base font-semibold text-nt-navy">Kondisi Desa</h2>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label for="kondisi_desa" class="mb-1.5 block text-sm font-medium text-slate-700">Kondisi Infrastruktur</label>
                            <textarea
                                name="kondisi_desa"
                                id="kondisi_desa"
                                rows="4"
                                placeholder="Deskripsikan kondisi jalan, akses listrik saat ini, dll..."
                                class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm shadow-sm placeholder:text-slate-400 focus:border-nt-navy focus:outline-none focus:ring-2 focus:ring-nt-navy/20"
                            >{{ old('kondisi_desa', $kondisi_infrastruktur) }}</textarea>
                            @error('kondisi_desa')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="jumlah_penduduk" class="mb-1.5 block text-sm font-medium text-slate-700">Jumlah Penduduk</label>
                                <div class="relative">
                                    <input type="number" name="jumlah_penduduk" id="jumlah_penduduk" value="{{ old('jumlah_penduduk', $jumlah_penduduk) }}" min="0" class="w-full rounded-lg border border-slate-300 py-2.5 pl-3 pr-14 text-sm shadow-sm focus:border-nt-navy focus:outline-none focus:ring-2 focus:ring-nt-navy/20" />
                                    <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-xs text-slate-400">jiwa</span>
                                </div>
                            </div>
                            <div>
                                <label for="jumlah_kk" class="mb-1.5 block text-sm font-medium text-slate-700">Jumlah KK</label>
                                <div class="relative">
                                    <input type="number" name="jumlah_kk" id="jumlah_kk" value="{{ old('jumlah_kk', $jumlah_kk) }}" min="0" class="w-full rounded-lg border border-slate-300 py-2.5 pl-3 pr-12 text-sm shadow-sm focus:border-nt-navy focus:outline-none focus:ring-2 focus:ring-nt-navy/20" />
                                    <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-xs text-slate-400">KK</span>
                                </div>
                            </div>
                        </div>
                         <div>
                            <span class="mb-2 block text-sm font-medium text-slate-700">Status Elektrifikasi</span>
                            <div class="flex flex-wrap gap-2">
                                @php $el = old('status_elektrifikasi', $status_elektrifikasi); @endphp
                                <label class="cursor-pointer">
                                    <input type="radio" name="status_elektrifikasi" value="belum_teraliri" class="peer sr-only" @checked($el === 'belum_teraliri') />
                                    <span class="inline-flex rounded-lg border-2 border-red-200 bg-white px-4 py-2 text-sm font-medium text-red-700 peer-checked:border-red-500 peer-checked:bg-red-50">Belum Teraliri</span>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="status_elektrifikasi" value="sebagian" class="peer sr-only" @checked($el === 'sebagian') />
                                    <span class="inline-flex rounded-lg border-2 border-amber-200 bg-white px-4 py-2 text-sm font-medium text-amber-800 peer-checked:border-amber-500 peer-checked:bg-amber-50">Sebagian</span>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="status_elektrifikasi" value="sudah_teraliri" class="peer sr-only" @checked($el === 'sudah_teraliri') />
                                    <span class="inline-flex rounded-lg border-2 border-emerald-200 bg-white px-4 py-2 text-sm font-medium text-emerald-800 peer-checked:border-emerald-500 peer-checked:bg-emerald-50">Sudah Teraliri</span>
                                </label>
                            </div>
                    </div>
                </section>

                {{-- Section C: Kebutuhan Energi --}}
                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="mb-5 flex items-center gap-2 border-b border-slate-100 pb-4">
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-sky-100 text-sky-700">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" /></svg>
                        </span>
                        <h2 class="text-base font-semibold text-nt-navy">Kebutuhan Energi</h2>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <span class="mb-2 block text-sm font-medium text-slate-700">Jenis Energi Potensial</span>
                            @php $sumber = old('sumber', $desa->sumber ?? 'solar_panel'); @endphp
                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                                @foreach ([
                                    'solar_panel' => ['label' => 'Solar Panel', 'icon' => '☀️'],
                                    'mikro_hidro' => ['label' => 'Mikro Hidro', 'icon' => '💧'],
                                    'biogas' => ['label' => 'Biogas', 'icon' => '♻️'],
                                    'hybrid' => ['label' => 'Hybrid', 'icon' => '⚡'],
                                ] as $val => $meta)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="sumber" value="{{ $val }}" class="peer sr-only" @checked($sumber === $val) />
                                        <div class="flex flex-col items-center gap-2 rounded-xl border-2 border-slate-200 bg-slate-50/50 p-4 text-center text-sm font-medium text-slate-700 shadow-sm transition-colors peer-checked:border-nt-navy peer-checked:bg-sky-50 peer-checked:text-nt-navy">
                                            <span class="text-2xl" aria-hidden="true">{{ $meta['icon'] }}</span>
                                            {{ $meta['label'] }}
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('sumber')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="estimasi_kebutuhan_daya" class="mb-1.5 block text-sm font-medium text-slate-700">Estimasi Kebutuhan Daya</label>
                                <div class="relative">
                                    <input type="text" name="estimasi_kebutuhan_daya" id="estimasi_kebutuhan_daya" value="{{ old('estimasi_kebutuhan_daya', $estimasi_kebutuhan_daya) }}" inputmode="decimal" class="w-full rounded-lg border border-slate-300 py-2.5 pl-3 pr-12 text-sm shadow-sm focus:border-nt-navy focus:outline-none focus:ring-2 focus:ring-nt-navy/20" />
                                    <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-xs font-medium text-slate-500">kW</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="catatan_tambahan" class="mb-1.5 block text-sm font-medium text-slate-700">Catatan Tambahan</label>
                            <textarea name="catatan_tambahan" id="catatan_tambahan" rows="3" placeholder="Informasi pendukung lainnya..." class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm shadow-sm placeholder:text-slate-400 focus:border-nt-navy focus:outline-none focus:ring-2 focus:ring-nt-navy/20">{{ old('catatan_tambahan', $catatan_tambahan) }}</textarea>
                        </div>
                    </div>
                </section>

                <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:justify-between">
                    <button type="submit" name="action" value="draft" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                        Simpan Draft
                    </button>
                    <button type="submit" name="action" value="submit" class="inline-flex items-center justify-center gap-1 rounded-lg bg-nt-accent px-6 py-2.5 text-sm font-bold text-nt-navy shadow-sm hover:bg-nt-accent-hover">
                        Update Data
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
                    </button>
                </div>
            </form>
        </div>

        {{-- Right column --}}
        <aside class="w-full shrink-0 space-y-4 lg:w-80">
            <div class="rounded-2xl bg-nt-navy p-5 text-white shadow-lg">
                <h3 class="mb-3 text-sm font-semibold">Panduan Edit</h3>
                <ul class="space-y-2 text-xs leading-relaxed text-white/85">
                    <li class="flex gap-2"><span class="text-nt-accent">✓</span> Pastikan koordinat GPS akurat (min. 4 desimal).</li>
                    <li class="flex gap-2"><span class="text-nt-accent">✓</span> Perbarui data kependudukan jika ada perubahan.</li>
                    <li class="flex gap-2"><span class="text-nt-accent">✓</span> Pastikan estimasi kebutuhan daya sudah sesuai dengan kondisi terkini.</li>
                </ul>
            </div>
        </aside>
    </div>
@endsection