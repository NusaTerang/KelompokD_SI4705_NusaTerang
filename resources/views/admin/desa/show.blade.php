@extends('layouts.admin')

@section('title', 'Detail Data Desa')

@section('breadcrumbs')
    <span>Dashboard</span>
    <span class="mx-1 text-slate-400">/</span>
    <span>Data Desa</span>
    <span class="mx-1 text-slate-400">/</span>
    <span class="font-medium text-slate-700">Detail Data</span>
@endsection

@section('page_heading', 'Detail Data Desa')

@push('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
@endpush

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

    <div class="mx-auto max-w-4xl space-y-8">
            <div class="space-y-8">

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
                                readonly
                                class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm shadow-sm text-slate-600 focus:outline-none"
                            />
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="provinsi" class="mb-1.5 block text-sm font-medium text-slate-700">Provinsi</label>
                                <input 
                                    type="text" 
                                    name="provinsi" 
                                    id="provinsi" 
                                    value="{{ old('provinsi', $desa->provinsi) }}" 
                                    readonly
                                    class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm shadow-sm text-slate-600 focus:outline-none"
                                />
                            </div>
                            <div>
                                <label for="kabupaten" class="mb-1.5 block text-sm font-medium text-slate-700">Kabupaten</label>
                                <input 
                                    type="text" 
                                    name="kabupaten" 
                                    id="kabupaten" 
                                    value="{{ old('kabupaten', $desa->kabupaten) }}" 
                                    readonly
                                    class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm shadow-sm text-slate-600 focus:outline-none"
                                />
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="kecamatan" class="mb-1.5 block text-sm font-medium text-slate-700">Kecamatan</label>
                                <input type="text" name="kecamatan" id="kecamatan" value="{{ old('kecamatan', $kecamatan) }}" readonly class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm shadow-sm text-slate-600 focus:outline-none" />
                            </div>
                            <div>
                                <label for="kode_wilayah" class="mb-1.5 block text-sm font-medium text-slate-700">Kode Wilayah</label>
                                <input type="text" name="kode_wilayah" id="kode_wilayah" value="{{ old('kode_wilayah', $kode_wilayah) }}" readonly class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm shadow-sm text-slate-600 focus:outline-none" />
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
                                        readonly
                                        class="w-full rounded-lg border border-slate-300 bg-slate-50 py-2.5 pl-10 pr-3 text-sm shadow-sm text-slate-600 focus:outline-none"
                                    />
                                </div>
                            </div>
                            <button type="button" id="btnOpenMap" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium shadow-sm transition-colors hover:bg-slate-50" style="color: #0f4c81;">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.602-1.538-1.425-1.637l-4.75-.598a1.125 1.125 0 00-1.175.906l-.01.006-4.25 2.525a1.125 1.125 0 01-1.175-.906l-.01-.006-4.25-2.525a1.125 1.125 0 00-1.175.906L3.35 13.537c-.19.946.514 1.821 1.452 1.987l4.875.975" /></svg>
                                Lihat di Peta
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
                                readonly
                                class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm shadow-sm text-slate-600 focus:outline-none"
                            >{{ old('kondisi_desa', $kondisi_infrastruktur) }}</textarea>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="jumlah_penduduk" class="mb-1.5 block text-sm font-medium text-slate-700">Jumlah Penduduk</label>
                                <div class="relative">
                                    <input type="number" name="jumlah_penduduk" id="jumlah_penduduk" value="{{ old('jumlah_penduduk', $jumlah_penduduk) }}" readonly class="w-full rounded-lg border border-slate-300 bg-slate-50 py-2.5 pl-3 pr-14 text-sm shadow-sm text-slate-600 focus:outline-none" />
                                    <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-xs text-slate-400">jiwa</span>
                                </div>
                            </div>
                            <div>
                                <label for="jumlah_kk" class="mb-1.5 block text-sm font-medium text-slate-700">Jumlah KK</label>
                                <div class="relative">
                                    <input type="number" name="jumlah_kk" id="jumlah_kk" value="{{ old('jumlah_kk', $jumlah_kk) }}" readonly class="w-full rounded-lg border border-slate-300 bg-slate-50 py-2.5 pl-3 pr-12 text-sm shadow-sm text-slate-600 focus:outline-none" />
                                    <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-xs text-slate-400">KK</span>
                                </div>
                            </div>
                        </div>
                         <div>
                            <span class="mb-2 block text-sm font-medium text-slate-700">Status Elektrifikasi</span>
                            <div class="flex flex-wrap gap-2">
                                @php $el = old('status_elektrifikasi', $status_elektrifikasi); @endphp
                                <label class="cursor-not-allowed opacity-75">
                                    <input type="radio" name="status_elektrifikasi" value="belum_teraliri" disabled class="peer sr-only" @checked($el === 'belum_teraliri') />
                                    <span class="inline-flex rounded-lg border-2 border-slate-200 bg-slate-50 px-4 py-2 text-sm font-medium text-slate-500 peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-700">Belum Teraliri</span>
                                </label>
                                <label class="cursor-not-allowed opacity-75">
                                    <input type="radio" name="status_elektrifikasi" value="sebagian" disabled class="peer sr-only" @checked($el === 'sebagian') />
                                    <span class="inline-flex rounded-lg border-2 border-slate-200 bg-slate-50 px-4 py-2 text-sm font-medium text-slate-500 peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-checked:text-amber-800">Sebagian</span>
                                </label>
                                <label class="cursor-not-allowed opacity-75">
                                    <input type="radio" name="status_elektrifikasi" value="sudah_teraliri" disabled class="peer sr-only" @checked($el === 'sudah_teraliri') />
                                    <span class="inline-flex rounded-lg border-2 border-slate-200 bg-slate-50 px-4 py-2 text-sm font-medium text-slate-500 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-800">Sudah Teraliri</span>
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
                                    <label class="cursor-not-allowed opacity-75">
                                        <input type="radio" name="sumber" value="{{ $val }}" disabled class="peer sr-only" @checked($sumber === $val) />
                                        <div class="flex flex-col items-center gap-2 rounded-xl border-2 border-slate-200 bg-slate-50 p-4 text-center text-sm font-medium text-slate-400 shadow-sm transition-colors peer-checked:border-nt-navy peer-checked:bg-sky-50 peer-checked:text-nt-navy">
                                            <span class="text-2xl" aria-hidden="true">{{ $meta['icon'] }}</span>
                                            {{ $meta['label'] }}
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="estimasi_kebutuhan_daya" class="mb-1.5 block text-sm font-medium text-slate-700">Estimasi Kebutuhan Daya</label>
                                <div class="relative">
                                    <input type="text" name="estimasi_kebutuhan_daya" id="estimasi_kebutuhan_daya" value="{{ old('estimasi_kebutuhan_daya', $estimasi_kebutuhan_daya) }}" readonly class="w-full rounded-lg border border-slate-300 bg-slate-50 py-2.5 pl-3 pr-12 text-sm shadow-sm text-slate-600 focus:outline-none" />
                                    <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-xs font-medium text-slate-500">kW</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="catatan_tambahan" class="mb-1.5 block text-sm font-medium text-slate-700">Catatan Tambahan</label>
                            <textarea name="catatan_tambahan" id="catatan_tambahan" rows="3" readonly class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm shadow-sm text-slate-600 focus:outline-none">{{ old('catatan_tambahan', $catatan_tambahan) }}</textarea>
                        </div>
                    </div>
                </section>

                <div class="flex justify-center border-t border-slate-200 pt-8">
                    <a href="{{ url()->previous() == url()->current() ? route('desa.kelola') : url()->previous() }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-white border-2 border-slate-200 px-8 py-3 text-sm font-bold text-slate-600 shadow-sm transition-colors hover:bg-slate-50 hover:border-slate-300">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                        Kembali
                    </a>
                </div>
            </div>
    </div>

    {{-- Map Modal --}}
    <div id="mapModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="mx-4 flex max-h-[90vh] w-full max-w-3xl flex-col rounded-2xl border border-slate-200 bg-white shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                <h3 class="text-base font-semibold" style="color: #0f4c81;">Lokasi di Peta</h3>
                <button type="button" id="closeMapModal" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
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

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                    closeModal();
                }
            });
        })();
    </script>
@endpush
