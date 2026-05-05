@extends('layouts.penyedia')

@section('title', 'Permintaan Proyek')

@section('breadcrumbs')
    <a href="{{ route('vendor.dashboard') }}" class="hover:text-deep-navy">Dashboard</a>
    <span class="material-symbols-outlined text-xs mx-1">chevron_right</span>
    <a href="{{ route('vendor.proyek.index') }}" class="hover:text-deep-navy">Proyek Saya</a>
    <span class="material-symbols-outlined text-xs mx-1">chevron_right</span>
    <span class="text-deep-navy font-bold">Permintaan Baru</span>
@endsection

@push('head')
    <script src="//unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush

@section('content')
@php
    $proyek  = $penugasan->proyek;
    $desa    = $proyek->desa;
    $creator = $proyek->creator;
    $detail  = $penugasan->detail;

    $waNumber = preg_replace('/[^0-9]/', '', $creator?->no_telepon ?? '');
    if ($waNumber && str_starts_with($waNumber, '0')) {
        $waNumber = '62' . substr($waNumber, 1);
    }
    $waMsg  = urlencode('Halo, saya dari ' . (auth()->user()->penyedia?->nama ?? 'Vendor') . '. Saya ingin berdiskusi mengenai proyek "' . $proyek->judul . '".');
    $waUrl  = $waNumber ? "https://wa.me/{$waNumber}?text={$waMsg}" : '#';

    $selectedEnergies = old('jenis_energi', $detail ? ($detail->jenis_energi ?? []) : []);
    $costBreakdown    = old('cost_breakdown', $detail ? ($detail->cost_breakdown ?? []) : []);

    $energiOptions = [
        ['value' => 'panel_surya',          'label' => 'Solar Panel',  'icon' => 'wb_sunny'],
        ['value' => 'mikro_hidro',          'label' => 'Mikro Hidro',  'icon' => 'water_drop'],
        ['value' => 'biogas',               'label' => 'Biogas',       'icon' => 'eco'],
        ['value' => 'hybrid_solar_baterai', 'label' => 'Hybrid',       'icon' => 'dynamic_form'],
    ];
@endphp

<div class="max-w-7xl mx-auto"
     x-data="{
         items: {{ count($costBreakdown) ? json_encode(array_map(fn($i) => ['nama' => is_array($i) ? ($i['nama'] ?? '') : ($i->nama ?? ''), 'nominal' => is_array($i) ? ($i['nominal'] ?? 0) : ($i->nominal ?? 0)], $costBreakdown)) : '[{nama:\'\',nominal:\'\'}]' }},
         addItem() { this.items.push({ nama: '', nominal: '' }); },
         removeItem(i) { if (this.items.length > 1) this.items.splice(i, 1); }
     }">

    {{-- Title --}}
    <div class="mb-4">
        <h2 class="text-3xl font-extrabold text-deep-navy tracking-tight">Permintaan Proyek Masuk</h2>
        <p class="text-on-surface-variant font-medium mt-1">Tinjau permintaan teknis dan kirimkan penawaran spesifikasi Anda.</p>
    </div>

    {{-- Alert Banner --}}
    @if($penugasan->status_penugasan === 'pending')
    <div class="bg-solar-gold/10 rounded-2xl p-5 flex items-center justify-between mb-8 border border-solar-gold/20 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-full bg-solar-gold flex items-center justify-center text-deep-navy shrink-0">
                <span class="material-symbols-outlined active-fill">notifications</span>
            </div>
            <div>
                <p class="text-sm text-deep-navy font-bold">Kamu mendapat permintaan proyek baru dari Admin</p>
                <p class="text-xs text-deep-navy/70 font-medium">Harap isi rincian teknis proyek agar tim kami dapat melakukan validasi lanjut.</p>
            </div>
        </div>
        <div class="px-4 py-2 bg-error text-white rounded-full flex items-center gap-2 text-xs font-bold shadow-lg shadow-error/20 shrink-0">
            <span class="material-symbols-outlined text-sm">schedule</span>
            Batas: 3 hari lagi
        </div>
    </div>
    @elseif($detail?->status === 'submitted')
    <div class="bg-sustainability-green/10 rounded-2xl p-5 flex items-center gap-4 mb-8 border border-sustainability-green/20">
        <span class="material-symbols-outlined text-sustainability-green active-fill">check_circle</span>
        <p class="text-sm text-sustainability-green font-bold">Rincian teknis sudah dikirim ke Admin dan sedang dalam proses peninjauan.</p>
    </div>
    @endif

    {{-- Two Column Layout --}}
    <div class="grid grid-cols-12 gap-8 items-start">

        {{-- LEFT: Project Info + Technical Form --}}
        <div class="col-span-12 lg:col-span-7 space-y-8">

            {{-- Section 1: Admin Info (Read-only) --}}
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-surface-container">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 rounded-lg bg-surface-container-low flex items-center justify-center text-deep-navy">
                        <span class="material-symbols-outlined">lock</span>
                    </div>
                    <h3 class="text-lg font-bold text-deep-navy">Informasi Proyek dari Admin</h3>
                </div>

                <div class="space-y-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-2">Nama Desa</label>
                            <div class="bg-surface-container-low px-4 py-3 rounded-lg text-deep-navy text-sm font-semibold">
                                {{ $desa->nama_desa ?? '-' }}{{ $desa && $desa->kecamatan ? ', ' . $desa->kecamatan : '' }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-2">Estimasi Pengerjaan</label>
                            <div class="bg-surface-container-low px-4 py-3 rounded-lg text-deep-navy text-sm font-semibold">
                                @if($proyek->estimasi_mulai && $proyek->estimasi_selesai)
                                    {{ $proyek->estimasi_mulai->format('d M Y') }} &ndash; {{ $proyek->estimasi_selesai->format('d M Y') }}
                                @else
                                    &mdash;
                                @endif
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-2">Judul Proyek</label>
                        <div class="bg-surface-container-low px-4 py-3 rounded-lg text-deep-navy text-sm font-semibold italic">
                            {{ $proyek->judul }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-2">Deskripsi Kebutuhan</label>
                        <div class="bg-surface-container-low px-4 py-3 rounded-lg text-on-surface-variant text-sm leading-relaxed">
                            {{ $proyek->deskripsi ?? 'Tidak ada deskripsi.' }}
                        </div>
                    </div>

                    @if($proyek->fotos->isNotEmpty())
                    <div>
                        <label class="block text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-2">Foto Lokasi Awal</label>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach($proyek->fotos->take(4) as $foto)
                            @php
                                $fotoUrl = str_starts_with($foto->path, 'http://') || str_starts_with($foto->path, 'https://')
                                    ? $foto->path
                                    : asset('storage/' . $foto->path);
                            @endphp
                            <div class="relative group overflow-hidden rounded-xl border border-surface-container h-40">
                                <img src="{{ $fotoUrl }}"
                                     alt="Foto Proyek"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                     onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'flex items-center justify-center h-full text-xs text-slate-400\'>Foto tidak tersedia</div>';">
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($creator)
                    <div>
                        <label class="block text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-2">PIC Admin</label>
                        <div class="bg-surface-container-low px-4 py-3 rounded-lg text-deep-navy text-sm font-semibold">
                            {{ $creator->nama }}
                            @if($creator->no_telepon)
                                <span class="text-on-surface-variant font-normal">({{ $creator->no_telepon }})</span>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Section 2: Technical Form --}}
            @if($detail?->status !== 'submitted')
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-surface-container">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 rounded-lg bg-surface-container-low flex items-center justify-center text-deep-navy">
                        <span class="material-symbols-outlined">edit_note</span>
                    </div>
                    <h3 class="text-lg font-bold text-deep-navy">Rincian Teknis Proyek</h3>
                </div>

                <form action="{{ route('vendor.proyek.detail', $penugasan->id_penugasan) }}" method="POST">
                    @csrf
                    @method('PUT')

                    @if($errors->any())
                        <div class="mb-6 bg-error/10 border border-error/30 rounded-xl p-4 text-sm text-error font-medium space-y-1">
                            @foreach($errors->all() as $err)
                                <p>{{ $err }}</p>
                            @endforeach
                        </div>
                    @endif

                    <div class="space-y-8">

                        {{-- Energy Type --}}
                        <div>
                            <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-4">
                                Pilih Jenis Energi Utama <span class="text-error">*</span>
                            </label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach($energiOptions as $opt)
                                @php $checked = in_array($opt['value'], $selectedEnergies); @endphp
                                <label class="flex flex-col items-center justify-center p-4 border-2 rounded-2xl cursor-pointer transition-all
                                             {{ $checked ? 'border-solar-gold bg-solar-gold/5' : 'border-surface-container hover:border-solar-gold/50' }}">
                                    <input type="checkbox"
                                           name="jenis_energi[]"
                                           value="{{ $opt['value'] }}"
                                           class="hidden"
                                           {{ $checked ? 'checked' : '' }}
                                           onchange="this.closest('label').classList.toggle('border-solar-gold', this.checked);
                                                      this.closest('label').classList.toggle('bg-solar-gold/5', this.checked);
                                                      this.closest('label').classList.toggle('border-surface-container', !this.checked);">
                                    <span class="material-symbols-outlined mb-2 text-2xl {{ $checked ? 'text-deep-navy active-fill' : 'text-on-surface-variant' }}">
                                        {{ $opt['icon'] }}
                                    </span>
                                    <span class="text-[10px] font-bold uppercase {{ $checked ? 'text-deep-navy' : 'text-on-surface-variant' }}">
                                        {{ $opt['label'] }}
                                    </span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Capacity & Target Dana --}}
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">
                                    Kapasitas Daya Terpasang <span class="text-error">*</span>
                                </label>
                                <div class="flex gap-2">
                                    <input type="number"
                                           name="kapasitas_daya"
                                           step="0.01"
                                           min="0"
                                           value="{{ old('kapasitas_daya', $detail?->kapasitas_daya) }}"
                                           placeholder="0.0"
                                           class="flex-1 bg-surface-container-low border-none rounded-xl px-4 py-4 text-sm font-semibold focus:ring-2 focus:ring-deep-navy">
                                    <select name="satuan_daya"
                                            class="w-24 bg-surface-container-low border-none rounded-xl px-3 py-4 text-xs font-bold focus:ring-2 focus:ring-deep-navy">
                                        @foreach(['kWp','kW','MW'] as $s)
                                            <option value="{{ $s }}" {{ old('satuan_daya', $detail?->satuan_daya ?? 'kWp') === $s ? 'selected' : '' }}>{{ $s }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">
                                    Target Dana Investasi (Rp) <span class="text-error">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-1 top-1/2 -translate-y-1/2 w-1 h-6 bg-solar-gold rounded-full"></span>
                                    <input type="number"
                                           name="target_dana"
                                           min="0"
                                           value="{{ old('target_dana', $detail?->target_dana) }}"
                                           placeholder="150000000"
                                           class="w-full bg-surface-container-low border-none rounded-xl pl-6 pr-4 py-4 text-sm font-bold text-deep-navy focus:ring-2 focus:ring-deep-navy">
                                </div>
                            </div>
                        </div>

                        {{-- Cost Breakdown --}}
                        <div>
                            <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-3">
                                Cost Breakdown (Rincian Anggaran)
                            </label>
                            <div class="space-y-3 mb-4">
                                <template x-for="(item, idx) in items" :key="idx">
                                    <div class="flex gap-3">
                                        <input type="text"
                                               :name="`cost_breakdown[${idx}][nama]`"
                                               x-model="item.nama"
                                               placeholder="Nama item (contoh: Panel Surya & Inverter)"
                                               class="flex-1 bg-surface-container-low border-none rounded-xl px-4 py-4 text-sm font-medium focus:ring-2 focus:ring-deep-navy">
                                        <input type="number"
                                               :name="`cost_breakdown[${idx}][nominal]`"
                                               x-model="item.nominal"
                                               placeholder="Nominal (Rp)"
                                               min="0"
                                               class="w-40 bg-surface-container-low border-none rounded-xl px-4 py-4 text-sm font-semibold text-deep-navy focus:ring-2 focus:ring-deep-navy">
                                        <button type="button"
                                                @click="removeItem(idx)"
                                                class="p-3 text-error hover:bg-error/10 rounded-xl transition-colors">
                                            <span class="material-symbols-outlined">delete</span>
                                        </button>
                                    </div>
                                </template>
                            </div>
                            <button type="button"
                                    @click="addItem()"
                                    class="flex items-center gap-2 text-deep-navy text-sm font-extrabold hover:bg-deep-navy/5 px-4 py-3 rounded-xl transition-all border-2 border-dashed border-deep-navy/20 w-full justify-center">
                                <span class="material-symbols-outlined text-sm">add_circle</span>
                                Tambah Item
                            </button>
                        </div>

                        {{-- Duration --}}
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">
                                    Durasi Pengerjaan <span class="text-error">*</span>
                                </label>
                                <div class="relative">
                                    <input type="number"
                                           name="durasi_minggu"
                                           min="1"
                                           value="{{ old('durasi_minggu', $detail?->durasi_minggu) }}"
                                           placeholder="12"
                                           class="w-full bg-surface-container-low border-none rounded-xl px-4 py-4 text-sm font-semibold focus:ring-2 focus:ring-deep-navy">
                                    <span class="absolute right-4 top-4 text-xs text-on-surface-variant font-bold">minggu</span>
                                </div>
                            </div>
                        </div>

                        {{-- Technical Notes --}}
                        <div>
                            <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-2">
                                Catatan Teknis &amp; Spesifikasi Material
                            </label>
                            <textarea name="catatan_teknis"
                                      rows="4"
                                      placeholder="Sebutkan merk komponen utama dan standar keamanan yang digunakan..."
                                      class="w-full bg-surface-container-low border-none rounded-2xl px-4 py-4 text-sm font-medium focus:ring-2 focus:ring-deep-navy">{{ old('catatan_teknis', $detail?->catatan_teknis) }}</textarea>
                        </div>

                        {{-- CTA Buttons --}}
                        <div class="flex flex-col sm:flex-row gap-3">
                            <button type="submit"
                                    name="save_draft"
                                    value="1"
                                    class="flex-1 py-4 border-2 border-deep-navy text-deep-navy rounded-2xl font-bold text-sm hover:bg-deep-navy hover:text-white transition-all uppercase tracking-wide">
                                Simpan sebagai Draft
                            </button>
                            <button type="submit"
                                    class="flex-1 py-5 bg-solar-gold hover:bg-solar-gold/90 text-deep-navy rounded-2xl font-black text-lg shadow-xl shadow-solar-gold/20 transition-all active:scale-95 uppercase tracking-wide">
                                Simpan &amp; Kirim Rincian
                            </button>
                        </div>

                    </div>
                </form>
            </div>
            @else
            {{-- Submitted state: show read-only summary --}}
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-surface-container">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-lg bg-sustainability-green/10 flex items-center justify-center text-sustainability-green">
                        <span class="material-symbols-outlined active-fill">task_alt</span>
                    </div>
                    <h3 class="text-lg font-bold text-deep-navy">Rincian Teknis Terkirim</h3>
                </div>
                <div class="grid grid-cols-2 gap-6 text-sm">
                    <div>
                        <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Kapasitas Daya</p>
                        <p class="font-bold text-deep-navy">{{ $detail->kapasitas_daya }} {{ $detail->satuan_daya }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Target Dana</p>
                        <p class="font-bold text-deep-navy">Rp {{ number_format($detail->target_dana, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Durasi Pengerjaan</p>
                        <p class="font-bold text-deep-navy">{{ $detail->durasi_minggu }} minggu</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-1">Jenis Energi</p>
                        <p class="font-bold text-deep-navy">
                            {{ implode(', ', array_map(fn($e) => match($e) {
                                'panel_surya' => 'Panel Surya', 'mikro_hidro' => 'Mikro Hidro',
                                'biogas' => 'Biogas', 'hybrid_solar_baterai' => 'Hybrid', default => $e
                            }, $detail->jenis_energi ?? [])) }}
                        </p>
                    </div>
                </div>
            </div>
            @endif

        </div>

        {{-- RIGHT: Actions (Sticky) --}}
        <div class="col-span-12 lg:col-span-5 sticky top-20 space-y-6">

            {{-- Action Card --}}
            <div class="bg-white rounded-2xl p-8 shadow-sm border-t-4 border-solar-gold border border-surface-container">
                <div class="flex justify-between items-start mb-8">
                    <div>
                        <h3 class="text-xl font-bold text-deep-navy">Tindakan</h3>
                        <p class="text-[10px] text-on-surface-variant font-bold mt-1">
                            Dibuat pada {{ $penugasan->created_at?->format('d M Y') ?? '-' }}
                        </p>
                    </div>
                    @php
                        $badge = match($penugasan->status_penugasan) {
                            'pending'  => 'Menunggu Konfirmasi',
                            'diterima' => 'Diterima',
                            'ditolak'  => 'Ditolak',
                            default    => '-',
                        };
                    @endphp
                    <span class="bg-solar-gold/10 text-deep-navy text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest">
                        {{ $badge }}
                    </span>
                </div>

                <div class="bg-surface-container-low p-5 rounded-2xl mb-8">
                    <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-4">Butuh Bantuan?</p>
                    <div class="space-y-3">
                        {{-- Minta Klarifikasi Modal Trigger --}}
                        <button type="button"
                                onclick="document.getElementById('modal-klarifikasi').classList.remove('hidden')"
                                class="w-full py-3 border-2 border-deep-navy text-deep-navy rounded-xl font-bold text-sm flex items-center justify-center gap-2 hover:bg-deep-navy hover:text-white transition-all">
                            <span class="material-symbols-outlined text-sm">forum</span>
                            Minta Klarifikasi
                        </button>
                        <button type="button"
                                onclick="document.getElementById('modal-kendala').classList.remove('hidden')"
                                class="w-full py-3 bg-surface-container-highest text-on-surface-variant rounded-xl font-bold text-sm flex items-center justify-center gap-2 hover:bg-error/10 hover:text-error transition-all">
                            <span class="material-symbols-outlined text-sm">report_problem</span>
                            Laporkan Kendala
                        </button>
                    </div>
                </div>

                <div class="pt-6 border-t border-surface-container">
                    <p class="text-xs text-on-surface-variant mb-4 font-medium">Hubungi PIC Project melalui jalur resmi:</p>
                    <div class="flex items-center gap-4 mb-4">
                        <div class="relative">
                            <div class="w-12 h-12 rounded-full bg-deep-navy flex items-center justify-center border border-surface-container">
                                <span class="text-white font-black text-sm">
                                    {{ strtoupper(substr($creator?->nama ?? 'A', 0, 2)) }}
                                </span>
                            </div>
                            <span class="absolute bottom-0 right-0 w-3 h-3 bg-sustainability-green border-2 border-white rounded-full"></span>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-deep-navy">{{ $creator?->nama ?? 'Tim NusaTerang' }}</p>
                            <p class="text-[10px] text-on-surface-variant font-bold">Dukungan Operasional</p>
                        </div>
                    </div>
                    <a href="{{ $waUrl }}"
                       target="_blank"
                       class="w-full py-4 bg-[#25D366] hover:bg-[#20bd5a] text-white rounded-xl font-bold flex items-center justify-center gap-3 shadow-lg shadow-[#25D366]/20 transition-all">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.246 2.248 3.484 5.232 3.484 8.412 0 6.556-5.338 11.891-11.893 11.891-2.01 0-3.99-.513-5.741-1.488l-6.256 1.697zm6.381-3.663c1.588.942 3.169 1.419 4.89 1.419 5.456 0 9.894-4.438 9.894-9.892 0-2.641-1.03-5.123-2.901-6.994-1.871-1.871-4.352-2.901-6.993-2.901-5.456 0-9.894 4.438-9.894 9.892 0 1.831.503 3.611 1.454 5.158l-1.018 3.716 3.844-1.042zm11.233-7.585c-.328-.164-1.944-.959-2.243-1.069-.3-.109-.519-.164-.738.164-.219.328-.847 1.069-1.038 1.289-.191.219-.383.246-.71.082-.328-.164-1.385-.511-2.637-1.629-.974-.87-1.631-1.944-1.821-2.272-.191-.328-.02-.505.144-.668.148-.147.328-.383.492-.574.164-.191.219-.328.328-.547.11-.219.055-.41-.027-.574-.082-.164-.738-1.777-1.011-2.433-.266-.64-.538-.553-.738-.563-.19-.01-.409-.011-.628-.011-.219 0-.574.082-.875.41-.301.328-1.148 1.121-1.148 2.733 0 1.612 1.175 3.169 1.339 3.388.164.219 2.312 3.529 5.6 4.951.782.338 1.393.54 1.869.691.785.25 1.498.215 2.063.129.629-.096 1.944-.793 2.217-1.558.273-.765.273-1.421.191-1.558-.081-.137-.3-.219-.628-.383z"/>
                        </svg>
                        Hubungi PIC (WhatsApp)
                    </a>
                </div>
            </div>

            {{-- Info Box --}}
            <div class="bg-sustainability-green/10 p-6 rounded-2xl border-l-4 border-sustainability-green flex items-start gap-4">
                <span class="material-symbols-outlined text-sustainability-green mt-0.5">info</span>
                <p class="text-xs text-deep-navy leading-relaxed font-medium">
                    Setelah rincian terisi, Admin akan mereview penawaran teknis Anda dalam waktu maksimal 48 jam sebelum proses penggalangan dana dimulai.
                </p>
            </div>

            {{-- Quote Visual --}}
            <div class="relative h-32 rounded-3xl overflow-hidden bg-slate-900 flex items-center justify-center p-6 text-center">
                <div class="absolute inset-0 opacity-30 bg-gradient-to-br from-deep-navy to-sustainability-green"></div>
                <p class="relative z-10 text-white font-headline font-bold text-sm leading-tight italic">
                    "Energy for the next generation, precision for today's investment."
                </p>
            </div>

        </div>
    </div>
</div>

{{-- Modal: Minta Klarifikasi --}}
<div id="modal-klarifikasi" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl p-8 w-full max-w-md shadow-2xl">
        <h3 class="text-xl font-bold text-deep-navy mb-4">Minta Klarifikasi</h3>
        <form action="{{ route('vendor.proyek.klarifikasi', $penugasan->id_penugasan) }}" method="POST">
            @csrf
            <textarea name="pertanyaan"
                      rows="4"
                      placeholder="Tuliskan pertanyaan atau hal yang perlu dikonfirmasi kepada Admin..."
                      required
                      class="w-full bg-surface-container-low border-none rounded-xl px-4 py-4 text-sm focus:ring-2 focus:ring-deep-navy mb-4"></textarea>
            <div class="flex gap-3">
                <button type="button"
                        onclick="document.getElementById('modal-klarifikasi').classList.add('hidden')"
                        class="flex-1 py-3 border-2 border-surface-container text-on-surface-variant rounded-xl font-bold text-sm hover:bg-surface-container transition-all">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 py-3 bg-deep-navy text-white rounded-xl font-bold text-sm hover:bg-deep-navy/90 transition-all">
                    Kirim Pertanyaan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Laporkan Kendala --}}
<div id="modal-kendala" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl p-8 w-full max-w-md shadow-2xl">
        <h3 class="text-xl font-bold text-deep-navy mb-4">Laporkan Kendala</h3>
        <form action="{{ route('vendor.proyek.kendala', $penugasan->id_penugasan) }}" method="POST">
            @csrf
            <textarea name="kendala"
                      rows="4"
                      placeholder="Deskripsikan kendala yang Anda hadapi..."
                      required
                      class="w-full bg-surface-container-low border-none rounded-xl px-4 py-4 text-sm focus:ring-2 focus:ring-deep-navy mb-4"></textarea>
            <div class="flex gap-3">
                <button type="button"
                        onclick="document.getElementById('modal-kendala').classList.add('hidden')"
                        class="flex-1 py-3 border-2 border-surface-container text-on-surface-variant rounded-xl font-bold text-sm hover:bg-surface-container transition-all">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 py-3 bg-error text-white rounded-xl font-bold text-sm hover:bg-error/90 transition-all">
                    Kirim Laporan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
