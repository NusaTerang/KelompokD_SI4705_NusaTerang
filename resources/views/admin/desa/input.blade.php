@extends('layouts.admin')

@section('title', 'Input Data Desa')

@section('breadcrumbs')
    <span>Dashboard</span>
    <span class="mx-1 text-slate-400">/</span>
    <span>Data Desa</span>
    <span class="mx-1 text-slate-400">/</span>
    <span class="font-medium text-slate-700">Input Data</span>
@endsection

@section('page_heading', 'Input Data Desa')

@push('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
@endpush

@section('content')
    <div class="mx-auto flex max-w-7xl flex-col gap-6 lg:flex-row lg:items-start">
        <div class="min-w-0 flex-1 space-y-6">
            {{-- Tabs --}}
            <div class="border-b border-slate-200">
                <div class="flex gap-8" role="tablist">
                    <button type="button" class="border-b-2 border-nt-accent pb-3 text-sm font-semibold text-nt-navy" aria-selected="true">
                        Input Manual
                    </button>
                    <button type="button" class="border-b-2 border-transparent pb-3 text-sm font-medium text-slate-500 hover:text-slate-700" aria-selected="false">
                        Import Excel
                    </button>
                </div>
            </div>

            <form action="{{ route('desa.store') }}" method="post" class="space-y-8">
                @csrf

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
                                value="{{ old('nama_desa') }}"
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
                                <select name="provinsi" id="provinsi" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nt-navy focus:outline-none focus:ring-2 focus:ring-nt-navy/20">
                                    <option value="">Pilih provinsi</option>
                                    <option value="Nusa Tenggara Timur" @selected(old('provinsi') === 'Nusa Tenggara Timur')>Nusa Tenggara Timur</option>
                                    <option value="Papua" @selected(old('provinsi') === 'Papua')>Papua</option>
                                    <option value="Kalimantan Timur" @selected(old('provinsi') === 'Kalimantan Timur')>Kalimantan Timur</option>
                                </select>
                                @error('provinsi')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="kabupaten" class="mb-1.5 block text-sm font-medium text-slate-700">Kabupaten</label>
                                <select name="kabupaten" id="kabupaten" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nt-navy focus:outline-none focus:ring-2 focus:ring-nt-navy/20">
                                    <option value="">Pilih kabupaten</option>
                                    <option value="Manggarai" @selected(old('kabupaten') === 'Manggarai')>Manggarai</option>
                                    <option value="Jayawijaya" @selected(old('kabupaten') === 'Jayawijaya')>Jayawijaya</option>
                                </select>
                                @error('kabupaten')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="kecamatan" class="mb-1.5 block text-sm font-medium text-slate-700">Kecamatan</label>
                                <input type="text" name="kecamatan" id="kecamatan" value="{{ old('kecamatan') }}" placeholder="Opsional — siap untuk migrasi kolom" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm shadow-sm focus:border-nt-navy focus:outline-none focus:ring-2 focus:ring-nt-navy/20" />
                            </div>
                            <div>
                                <label for="kode_wilayah" class="mb-1.5 block text-sm font-medium text-slate-700">Kode Wilayah</label>
                                <input type="text" name="kode_wilayah" id="kode_wilayah" value="{{ old('kode_wilayah') }}" placeholder="Opsional" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm shadow-sm focus:border-nt-navy focus:outline-none focus:ring-2 focus:ring-nt-navy/20" />
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
                                        value="{{ old('koordinat') }}"
                                        placeholder="-8.1234, 115.5678"
                                        class="w-full rounded-lg border border-slate-300 py-2.5 pl-10 pr-3 text-sm shadow-sm focus:border-nt-navy focus:outline-none focus:ring-2 focus:ring-nt-navy/20"
                                    />
                                </div>
                                @error('koordinat')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <button type="button" id="btnOpenMap" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-nt-navy shadow-sm hover:bg-slate-50">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.602-1.538-1.425-1.637l-4.75-.598a1.125 1.125 0 00-1.175.906l-.01.006-4.25 2.525a1.125 1.125 0 01-1.175-.906l-.01-.006-4.25-2.525a1.125 1.125 0 00-1.175.906L3.35 13.537c-.19.946.514 1.821 1.452 1.987l4.875.975" /></svg>
                                Tandai di Peta
                            </button>
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
                            >{{ old('kondisi_desa') }}</textarea>
                            @error('kondisi_desa')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="jumlah_penduduk" class="mb-1.5 block text-sm font-medium text-slate-700">Jumlah Penduduk</label>
                                <div class="relative">
                                    <input type="number" name="jumlah_penduduk" id="jumlah_penduduk" value="{{ old('jumlah_penduduk') }}" min="0" class="w-full rounded-lg border border-slate-300 py-2.5 pl-3 pr-14 text-sm shadow-sm focus:border-nt-navy focus:outline-none focus:ring-2 focus:ring-nt-navy/20" />
                                    <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-xs text-slate-400">jiwa</span>
                                </div>
                            </div>
                            <div>
                                <label for="jumlah_kk" class="mb-1.5 block text-sm font-medium text-slate-700">Jumlah KK</label>
                                <div class="relative">
                                    <input type="number" name="jumlah_kk" id="jumlah_kk" value="{{ old('jumlah_kk') }}" min="0" class="w-full rounded-lg border border-slate-300 py-2.5 pl-3 pr-12 text-sm shadow-sm focus:border-nt-navy focus:outline-none focus:ring-2 focus:ring-nt-navy/20" />
                                    <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-xs text-slate-400">KK</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <span class="mb-2 block text-sm font-medium text-slate-700">Status Elektrifikasi</span>
                            <div class="flex flex-wrap gap-2">
                                @php $el = old('status_elektrifikasi', 'sebagian'); @endphp
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
                            @php $sumber = old('sumber', 'solar_panel'); @endphp
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
                                    <input type="text" name="estimasi_kebutuhan_daya" id="estimasi_kebutuhan_daya" value="{{ old('estimasi_kebutuhan_daya') }}" inputmode="decimal" class="w-full rounded-lg border border-slate-300 py-2.5 pl-3 pr-12 text-sm shadow-sm focus:border-nt-navy focus:outline-none focus:ring-2 focus:ring-nt-navy/20" />
                                    <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-xs font-medium text-slate-500">kW</span>
                                </div>
                            </div>
                            <div>
                                <label for="status_verifikasi" class="mb-1.5 block text-sm font-medium text-slate-700">Status Verifikasi</label>
                                <select name="status_verifikasi" id="status_verifikasi" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-nt-navy focus:outline-none focus:ring-2 focus:ring-nt-navy/20">
                                    @foreach (['draft' => 'Draft', 'menunggu_verifikasi' => 'Menunggu Verifikasi', 'terverifikasi' => 'Terverifikasi', 'ditolak' => 'Ditolak'] as $val => $label)
                                        <option value="{{ $val }}" @selected(old('status_verifikasi', 'menunggu_verifikasi') === $val)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('status_verifikasi')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="catatan_tambahan" class="mb-1.5 block text-sm font-medium text-slate-700">Catatan Tambahan</label>
                            <textarea name="catatan_tambahan" id="catatan_tambahan" rows="3" placeholder="Informasi pendukung lainnya..." class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm shadow-sm placeholder:text-slate-400 focus:border-nt-navy focus:outline-none focus:ring-2 focus:ring-nt-navy/20">{{ old('catatan_tambahan') }}</textarea>
                        </div>
                    </div>
                </section>

                <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:justify-between">
                    <button type="submit" name="action" value="draft" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                        Simpan Draft
                    </button>
                    <button type="submit" name="action" value="submit" class="inline-flex items-center justify-center gap-1 rounded-lg bg-nt-accent px-6 py-2.5 text-sm font-bold text-nt-navy shadow-sm hover:bg-nt-accent-hover">
                        Ajukan Data
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
                    </button>
                </div>
            </form>
        </div>

        {{-- Right column --}}
        <aside class="w-full shrink-0 space-y-4 lg:w-80">
            <div class="rounded-2xl bg-nt-navy p-5 text-white shadow-lg">
                <h3 class="mb-3 text-sm font-semibold">Panduan Input</h3>
                <ul class="space-y-2 text-xs leading-relaxed text-white/85">
                    <li class="flex gap-2"><span class="text-nt-accent">✓</span> Pastikan koordinat GPS akurat (min. 4 desimal).</li>
                    <li class="flex gap-2"><span class="text-nt-accent">✓</span> Gunakan data kependudukan resmi desa / BPS jika ada.</li>
                    <li class="flex gap-2"><span class="text-nt-accent">✓</span> Lampirkan foto lokasi saat mengajukan (fitur unggah menyusul).</li>
                </ul>
                <a href="#" class="mt-4 inline-flex items-center gap-2 text-xs font-medium text-nt-accent hover:underline">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                    Unduh Dokumen Panduan (.pdf)
                </a>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-100/80 p-5 shadow-sm">
                <h3 class="mb-3 text-sm font-semibold text-nt-navy">Aktivitas Terakhir</h3>
                <ul class="space-y-3 text-xs">
                    <li class="flex gap-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                        </span>
                        <div>
                            <p class="font-medium text-slate-800">Desa Mandiri Sejahtera</p>
                            <p class="text-slate-500">Baru saja diajukan</p>
                        </div>
                    </li>
                    <li class="flex gap-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-sky-100 text-sky-700">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487z" /></svg>
                        </span>
                        <div>
                            <p class="font-medium text-slate-800">Draft: Desa Sukajaya</p>
                            <p class="text-slate-500">2 jam yang lalu</p>
                        </div>
                    </li>
                </ul>
            </div>
        </aside>
    </div>

    {{-- Map Modal --}}
    <div id="mapModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="mx-4 flex max-h-[90vh] w-full max-w-3xl flex-col rounded-2xl border border-slate-200 bg-white shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                <h3 class="text-base font-semibold text-nt-navy">Tandai Lokasi di Peta</h3>
                <button type="button" id="closeMapModal" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="flex items-center gap-3 border-b border-slate-100 bg-slate-50 px-6 py-3">
                <button type="button" id="btnGeolokasi" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 shadow-sm hover:bg-slate-50">
                    <svg class="h-4 w-4 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                    Gunakan Lokasi Saya
                </button>
                <span class="text-xs text-slate-500">Klik pada peta untuk menandai koordinat.</span>
            </div>
            <div id="mapContainer" class="h-[400px] w-full rounded-b-2xl"></div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        (function() {
            const modal = document.getElementById('mapModal');
            const openBtn = document.getElementById('btnOpenMap');
            const closeBtn = document.getElementById('closeMapModal');
            const koordinatInput = document.getElementById('koordinat');
            const btnGeolokasi = document.getElementById('btnGeolokasi');
            let map = null;
            let marker = null;

            function openModal() {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                if (!map) {
                    requestAnimationFrame(function() {
                        initMap();
                        setTimeout(function() { map.invalidateSize(); }, 100);
                    });
                } else {
                    setTimeout(function() { map.invalidateSize(); }, 50);
                }
            }

            function closeModal() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            function initMap() {
                map = L.map('mapContainer').setView([-2.5, 118], 5);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                const existing = parseCoords(koordinatInput.value);
                if (existing) {
                    placeMarker(existing.lat, existing.lng, true);
                }

                map.on('click', function(e) {
                    placeMarker(e.latlng.lat, e.latlng.lng, false);
                });
            }

            function parseCoords(val) {
                if (!val) return null;
                const parts = val.split(',').map(s => parseFloat(s.trim()));
                if (parts.length === 2 && !isNaN(parts[0]) && !isNaN(parts[1])) {
                    return { lat: parts[0], lng: parts[1] };
                }
                return null;
            }

            function placeMarker(lat, lng, pan) {
                if (marker) map.removeLayer(marker);
                marker = L.marker([lat, lng]).addTo(map);
                koordinatInput.value = lat.toFixed(6) + ', ' + lng.toFixed(6);
                if (pan) map.setView([lat, lng], 14);
            }

            openBtn.addEventListener('click', openModal);
            closeBtn.addEventListener('click', closeModal);
            modal.addEventListener('click', function(e) {
                if (e.target === modal) closeModal();
            });

            btnGeolokasi.addEventListener('click', function() {
                if (!navigator.geolocation) {
                    alert('Browser tidak mendukung geolokasi.');
                    return;
                }
                btnGeolokasi.disabled = true;
                btnGeolokasi.innerHTML = '<svg class="h-4 w-4 animate-spin text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182" /></svg> Mencari...';
                navigator.geolocation.getCurrentPosition(function(pos) {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    placeMarker(lat, lng, true);
                    btnGeolokasi.disabled = false;
                    btnGeolokasi.innerHTML = '<svg class="h-4 w-4 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg> Gunakan Lokasi Saya';
                }, function() {
                    alert('Gagal mendapatkan lokasi. Pastikan izin lokasi diaktifkan.');
                    btnGeolokasi.disabled = false;
                    btnGeolokasi.innerHTML = '<svg class="h-4 w-4 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg> Gunakan Lokasi Saya';
                });
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                    closeModal();
                }
            });
        })();
    </script>
@endpush
