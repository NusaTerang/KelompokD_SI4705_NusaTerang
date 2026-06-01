@extends('layouts.admin')

@section('title', 'Publikasi Proyek')

@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-700">Dashboard</a>
    <span class="mx-2">&rsaquo;</span>
    <a href="{{ route('proyek.kelola') }}" class="hover:text-slate-700">Proyek Energi</a>
    <span class="mx-2">&rsaquo;</span>
    <span class="font-medium text-slate-900">Publikasi</span>
@endsection

@section('topbar_actions')
    <div class="flex items-center gap-5 mr-2">
        {{-- Status Badge --}}
        <div class="flex items-center gap-2.5 bg-[#fffdf0] text-[#0f4c81] px-5 py-2 rounded-full text-[13px] font-bold border border-[#fbf4c4]">
            <div class="w-2.5 h-2.5 rounded-full bg-[#0f4c81]"></div>
            Menunggu Publikasi
        </div>

        {{-- Save Draft Button --}}
        <form id="draftForm" action="{{ route('proyek.save.draft', $proyek->id) }}" method="POST" class="hidden">
            @csrf
        </form>
        <button type="submit" form="draftForm" class="text-[14px] font-semibold text-slate-600 hover:text-slate-900 mx-2">
            Save Draft
        </button>

        {{-- Publish Button (Topbar) --}}
        <button type="submit" form="publishForm"
            class="text-[14px] font-bold {{ $isLengkap ? 'bg-[#FDD835] text-slate-900 hover:opacity-90' : 'bg-slate-200 text-slate-400 cursor-not-allowed' }} px-6 py-2.5 rounded-full transition-opacity"
            {{ $isLengkap ? '' : 'disabled' }}>
            Publish Project
        </button>
    </div>
@endsection

@section('content')
<div class="max-w-[1200px] mx-auto pb-10">

    {{-- ═══════════ TOMBOL KEMBALI ═══════════ --}}
    <div class="mb-6">
        <a href="{{ route('proyek.kelola') }}"
           class="inline-flex items-center gap-2 text-[14px] font-bold text-slate-500 hover:text-slate-800 transition-colors bg-white px-4 py-2 rounded-xl shadow-[0_2px_8px_rgba(0,0,0,0.04)] border border-slate-100 w-fit">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Kembali
        </a>
    </div>

    {{-- ═══════════ KELENGKAPAN PROYEK ═══════════ --}}
    <div class="bg-white rounded-2xl shadow-[0_2px_8px_rgba(0,0,0,0.04)] p-6 md:p-8 mb-8 border border-slate-100">
        <div class="flex flex-col md:flex-row justify-between items-center gap-8">

            {{-- Checklist Kiri --}}
            <div class="w-full md:w-3/5 md:pr-4">
                <h2 class="text-[20px] font-bold text-slate-800 flex items-center gap-2 mb-6">
                    <span class="material-symbols-outlined text-green-600 text-[24px]">fact_check</span>
                    Kelengkapan Proyek
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-y-4 gap-x-2">
                    {{-- Kolom 1 --}}
                    <div class="flex flex-col gap-4">
                        <div class="flex items-center gap-2 text-[13px] font-semibold text-slate-700">
                            <span class="material-symbols-outlined {{ $reqDetail ? 'text-green-600' : 'text-slate-300' }} text-[18px]" style="font-variation-settings: 'FILL' 1;">{{ $reqDetail ? 'check_circle' : 'radio_button_unchecked' }}</span>
                            Detail Proyek
                        </div>
                        <div class="flex items-center gap-2 text-[13px] font-semibold text-slate-700">
                            <span class="material-symbols-outlined {{ $reqRincian ? 'text-green-600' : 'text-slate-300' }} text-[18px]" style="font-variation-settings: 'FILL' 1;">{{ $reqRincian ? 'check_circle' : 'radio_button_unchecked' }}</span>
                            Rincian Teknis
                        </div>
                    </div>
                    {{-- Kolom 2 --}}
                    <div class="flex flex-col gap-4">
                        <div class="flex items-center gap-2 text-[13px] font-semibold text-slate-700">
                            <span class="material-symbols-outlined {{ $reqFoto ? 'text-green-600' : 'text-slate-300' }} text-[18px]" style="font-variation-settings: 'FILL' 1;">{{ $reqFoto ? 'check_circle' : 'radio_button_unchecked' }}</span>
                            Foto Proyek
                        </div>
                        <div class="flex items-center gap-2 text-[13px] font-semibold text-slate-700">
                            <span class="material-symbols-outlined {{ $reqTarget ? 'text-green-600' : 'text-slate-300' }} text-[18px]" style="font-variation-settings: 'FILL' 1;">{{ $reqTarget ? 'check_circle' : 'radio_button_unchecked' }}</span>
                            Target Dana
                        </div>
                    </div>
                    {{-- Kolom 3 --}}
                    <div class="flex flex-col gap-4">
                        <div class="flex items-center gap-2 text-[13px] font-semibold text-slate-700">
                            <span class="material-symbols-outlined {{ $reqPenyedia ? 'text-green-600' : 'text-slate-300' }} text-[18px]" style="font-variation-settings: 'FILL' 1;">{{ $reqPenyedia ? 'check_circle' : 'radio_button_unchecked' }}</span>
                            Penyedia Energi Dipilih
                        </div>
                    </div>
                </div>
            </div>

            {{-- Progress Bar Kanan --}}
            <div class="w-full md:w-2/5 md:pl-8 flex flex-col justify-center mt-4 md:mt-0 md:border-l md:border-slate-100">
                <div class="flex justify-between items-end mb-3">
                    <span class="text-[12px] font-extrabold {{ $isLengkap ? 'text-green-600' : 'text-slate-500' }} tracking-wider">{{ $completedCount }}/5 LENGKAP</span>
                    <span class="text-[12px] font-semibold {{ $isLengkap ? 'text-[#0f4c81]' : 'text-slate-400' }}">{{ $isLengkap ? 'Siap Publikasi' : 'Belum Lengkap' }}</span>
                </div>
                <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden mb-3">
                    <div class="h-full bg-[#FDD835]" style="width: {{ ($completedCount / 5) * 100 }}%"></div>
                </div>
                <p class="text-[12px] font-bold {{ $isLengkap ? 'text-green-600' : 'text-slate-400' }} flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">{{ $isLengkap ? 'check_circle' : 'info' }}</span>
                    {{ $isLengkap ? 'Proyek siap dipublikasikan ke ekosistem' : 'Lengkapi data untuk publikasi' }}
                </p>
            </div>
        </div>
    </div>

    {{-- ═══════════ KONTEN UTAMA 2 KOLOM ═══════════ --}}
    <div class="grid grid-cols-1 md:grid-cols-12 gap-8">

        {{-- ─── KIRI: PREVIEW HALAMAN PUBLIK ─── --}}
        <div class="md:col-span-7">
            <div class="bg-white rounded-2xl shadow-[0_2px_8px_rgba(0,0,0,0.04)] border border-slate-100 overflow-hidden">

                {{-- Cover Image --}}
                @php
                    $fotoUtama = $proyek->fotos->first();
                    $fotoUrl = $fotoUtama
                        ? (str_starts_with($fotoUtama->path, 'http') ? $fotoUtama->path : asset('storage/' . $fotoUtama->path))
                        : null;
                        
                    $baseDana = (float)($detail->target_dana ?? 0);
                    $platformFee = $baseDana * 0.05;
                    $calculatedTargetDana = $baseDana + $platformFee;
                    $finalTargetDana = $calculatedTargetDana > 0 ? $calculatedTargetDana : ($proyek->target_dana ?? 0);

                    $formatNominal = function($nominal) {
                        $nominal = round((float)$nominal);
                        if ($nominal >= 1000000) {
                            $jt = $nominal / 1000000;
                            return (floor($jt) == $jt ? number_format($jt, 0, ',', '.') : number_format($jt, 1, ',', '.')) . 'jt';
                        } elseif ($nominal >= 1000) {
                            $rb = $nominal / 1000;
                            return (floor($rb) == $rb ? number_format($rb, 0, ',', '.') : number_format($rb, 1, ',', '.')) . 'rb';
                        } else {
                            return number_format($nominal, 0, ',', '.');
                        }
                    };
                @endphp
                <div class="relative h-[340px] bg-slate-200 w-full">
                    <div class="absolute top-0 right-0 bg-[#fff9c4] px-5 py-2.5 rounded-bl-2xl z-10">
                        <span class="text-[10px] font-extrabold text-[#0f4c81] tracking-widest uppercase">PREVIEW HALAMAN</span>
                    </div>
                    @if($fotoUrl)
                        <img src="{{ $fotoUrl }}" alt="Cover Proyek" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-400">
                            <span class="material-symbols-outlined text-5xl">image</span>
                        </div>
                    @endif
                    <div class="absolute bottom-5 left-6 flex gap-2.5">
                        <span class="px-4 py-2 bg-[#2c2c2c] text-white text-[10px] font-bold uppercase tracking-widest rounded-full shadow-sm">Energi Terbarukan</span>
                        <span class="px-4 py-2 bg-[#105a30] text-white text-[10px] font-bold uppercase tracking-widest rounded-full shadow-sm">Dampak Tinggi</span>
                    </div>
                </div>

                {{-- Konten Preview --}}
                <div class="p-6 md:p-8">
                    <h1 class="text-2xl md:text-[28px] font-extrabold text-[#0f4c81] tracking-tight leading-tight mb-4">
                        {{ $proyek->judul }}
                    </h1>

                    <div class="flex items-center gap-1.5 text-[14px] text-slate-600 font-medium mb-7">
                        <span class="material-symbols-outlined text-[#FDD835] text-[20px]" style="font-variation-settings: 'FILL' 1;">location_on</span>
                        {{ $proyek->desa->nama_desa ?? '-' }}, {{ $proyek->desa->kabupaten ?? '-' }}, {{ $proyek->desa->provinsi ?? '-' }}
                    </div>

                    <div class="text-slate-600 text-[15px] leading-relaxed mb-10">
                        {{ $proyek->deskripsi ?? 'Deskripsi proyek belum tersedia.' }}
                    </div>

                    {{-- Progress Funding Box --}}
                    <div class="bg-[#f8fafc] rounded-2xl p-6 md:p-8 mb-8 border border-slate-100">
                        <div class="flex justify-between items-end mb-3">
                            <div class="flex items-baseline gap-1">
                                <span class="text-[28px] font-extrabold text-[#0f4c81]">Rp 0</span>
                                <span class="text-[14px] text-slate-500 font-medium ml-1">dari Rp {{ number_format($finalTargetDana, 0, ',', '.') }}</span>
                            </div>
                            <span class="text-[20px] font-extrabold text-[#0f4c81]">0%</span>
                        </div>
                        <div class="w-full h-3 bg-[#e2e8f0] rounded-full overflow-hidden mb-8">
                            <div class="w-0 h-full bg-[#0f4c81]"></div>
                        </div>

                        <div class="grid grid-cols-3 gap-4 text-center">
                            <div class="bg-white py-5 rounded-xl shadow-[0_2px_8px_rgba(0,0,0,0.02)] border border-slate-50">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Donatur</p>
                                <p class="text-[22px] font-extrabold text-[#0f4c81]">0</p>
                            </div>
                            <div class="bg-white py-5 rounded-xl shadow-[0_2px_8px_rgba(0,0,0,0.02)] border border-slate-50">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Sisa Hari</p>
                                <p class="text-[22px] font-extrabold text-[#0f4c81]">{{ $detail?->durasi_minggu ? ($detail->durasi_minggu * 7) : 0 }}</p>
                            </div>
                            <div class="bg-white py-5 rounded-xl shadow-[0_2px_8px_rgba(0,0,0,0.02)] border border-slate-50">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Status</p>
                                <p class="text-[15px] mt-1.5 font-bold text-green-600">Review</p>
                            </div>
                        </div>
                    </div>

                    {{-- Provider Info --}}
                    <div class="flex items-center justify-between mt-10 border-t border-slate-100 pt-8">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-[#0f4c81] text-white rounded-full flex items-center justify-center font-bold text-lg shadow-md">
                                PT
                            </div>
                            <div>
                                <p class="text-[15px] font-bold text-[#0f4c81] mb-1.5">Penyedia: {{ $proyek->penyedia->nama ?? 'PT. Surya Mandiri' }}</p>
                                <div class="flex gap-2">
                                    <span class="px-2.5 py-1 bg-slate-100 text-slate-500 text-[10px] rounded font-semibold">{{ ucfirst(str_replace('_', ' ', $proyek->penyedia->spesialisasi ?? 'Solar')) }}</span>
                                    <span class="px-2.5 py-1 bg-slate-100 text-slate-500 text-[10px] rounded font-semibold">Maintenance Incl.</span>
                                </div>
                            </div>
                        </div>
                        <button class="flex items-center gap-2 bg-[#fff9c4] text-[#a79815] px-6 py-3 rounded-xl font-bold text-[14px] cursor-not-allowed opacity-90">
                            <span class="material-symbols-outlined text-[18px]">lock</span> DONASI SEKARANG
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─── KANAN: SIDEBAR RINCIAN TEKNIS + AKSI ─── --}}
        <div class="md:col-span-5 space-y-6">

            {{-- Card Rincian Teknis --}}
            <div class="bg-white rounded-2xl shadow-[0_2px_12px_rgba(0,0,0,0.04)] border-[1.5px] border-[#FDD835] p-6 md:p-8 relative overflow-hidden">
                <h3 class="text-[20px] font-extrabold text-[#0f4c81] mb-8 flex items-center gap-2.5">
                    <span class="material-symbols-outlined text-[#FDD835] text-[24px]">engineering</span> Rincian Teknis
                </h3>

                {{-- Kapasitas & Durasi --}}
                <div class="grid grid-cols-2 gap-4 mb-8">
                    <div class="bg-[#f8fafc] rounded-2xl p-5 border border-slate-100 text-left">
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5">Kapasitas</p>
                        <p class="text-[16px] font-extrabold text-[#0f4c81]">{{ $detail->kapasitas_daya ?? '-' }} {{ $detail->satuan_daya ?? '' }}</p>
                    </div>
                    <div class="bg-[#f8fafc] rounded-2xl p-5 border border-slate-100 text-left">
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5">Durasi Kerja</p>
                        <p class="text-[16px] font-extrabold text-[#0f4c81]">{{ $detail?->durasi_minggu ? ($detail->durasi_minggu * 7) : '-' }} Hari Kalender</p>
                    </div>
                </div>

                {{-- Target Dana --}}
                <div class="mb-8">
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2.5">Target Dana Proyek</p>
                    <div class="bg-[#fffdf0] border border-[#fbf4c4] rounded-2xl p-6 flex items-center">
                        <p class="text-[32px] font-black text-[#0f4c81]">Rp {{ number_format($finalTargetDana, 0, ',', '.') }}</p>
                    </div>
                </div>

                {{-- Breakdown Anggaran --}}
                <div class="mb-8">
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2.5">Breakdown Anggaran</p>
                    <div class="bg-[#f8fafc] rounded-2xl border border-slate-100 overflow-hidden text-[14px]">
                        @if($detail && !empty($detail->cost_breakdown))
                            @foreach($detail->cost_breakdown as $item)
                            <div class="flex justify-between items-center px-5 py-3.5 border-b border-slate-100 last:border-0">
                                <span class="text-slate-500 font-medium">{{ $item['nama'] ?? '-' }}</span>
                                <span class="font-bold text-slate-800">Rp {{ $formatNominal($item['nominal'] ?? 0) }}</span>
                            </div>
                            @endforeach
                        @else
                            <div class="p-5 text-center text-slate-400 font-medium">Belum ada breakdown anggaran</div>
                        @endif
                        <div class="flex justify-between items-center px-5 py-4 bg-[#fff9c4] border-t border-[#f2eaad] font-bold">
                            <span class="text-slate-800 text-[13px]">Total Platform Fee (5%)</span>
                            <span class="text-slate-800">Rp {{ $formatNominal($platformFee) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Catatan Teknis Penyedia --}}
                <div class="mb-8">
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2.5">Catatan Teknis Penyedia</p>
                    <div class="bg-[#f8fafc] rounded-2xl border border-slate-100 p-5 text-[14px] text-slate-500 font-medium leading-relaxed">
                        "{{ $detail->catatan_teknis ?? 'Tidak ada catatan khusus dari penyedia untuk proyek ini.' }}"
                    </div>
                </div>

                {{-- Form Publikasi --}}
                <form id="publishForm" action="{{ route('proyek.publish', $proyek->id) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    {{-- Jadwalkan Publikasi --}}
                    <div class="mb-8">
                        <label class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2 block">Jadwalkan Publikasi</label>
                        <div class="relative bg-[#f8fafc] rounded-xl border border-slate-200 overflow-hidden flex items-center pr-3">
                            <input type="text" name="jadwal_publikasi" placeholder="mm/dd/yyyy, --:-- --" class="w-full bg-transparent border-none px-5 py-3.5 text-[14px] text-slate-700 focus:outline-none placeholder-slate-400 font-medium">
                            <span class="material-symbols-outlined text-slate-400 text-[20px]">calendar_month</span>
                        </div>
                        <p class="text-[10px] text-slate-400 italic mt-2.5">Biarkan kosong untuk publikasi instan.</p>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="space-y-3.5 mt-10">
                        {{-- Tombol Publikasi --}}
                        <button type="submit"
                            class="w-full {{ $isLengkap ? 'bg-[#FDD835] hover:opacity-90 text-slate-900 shadow-[0_2px_4px_rgba(0,0,0,0.05)]' : 'bg-slate-200 text-slate-400 cursor-not-allowed' }} font-extrabold text-[16px] py-4 rounded-xl transition-all flex justify-center items-center gap-2"
                            {{ $isLengkap ? '' : 'disabled' }}>
                            Publikasikan Sekarang
                        </button>

                        {{-- Peringatan jika belum lengkap --}}
                        @if(!$isLengkap)
                        <div class="text-[11px] text-red-500 font-bold flex items-center justify-center gap-1.5 bg-red-50 py-2.5 rounded-lg border border-red-100 mt-2">
                            <span class="material-symbols-outlined text-[14px]">error</span>
                            Harap lengkapi semua data ({{ $completedCount }}/5) untuk mempublikasikan proyek.
                        </div>
                        @endif

                        {{-- Kembalikan ke Penyedia --}}
                        <button type="button" onclick="document.getElementById('modalKembalikan').classList.remove('hidden')"
                            class="w-full bg-white hover:bg-slate-50 border border-slate-200 text-slate-500 font-bold text-[16px] py-4 rounded-xl transition-all mt-4 block">
                            Kembalikan ke Penyedia
                        </button>
                    </div>
                </form>
            </div>

            {{-- Card Verifikasi Legal --}}
            <div class="bg-[#0f4c81] rounded-2xl p-6 text-white shadow-[0_2px_10px_rgba(0,0,0,0.1)] relative overflow-hidden">
                <div class="flex items-center gap-2 mb-4">
                    <span class="material-symbols-outlined text-[#FDD835] text-[20px]" style="font-variation-settings: 'FILL' 1;">verified</span>
                    <h4 class="font-bold tracking-widest uppercase text-[14px]">Verifikasi Legal</h4>
                </div>
                <p class="text-[14px] text-white/90 leading-relaxed mb-6 pr-4">
                    Izin Prinsip dan IMB PLTS telah divalidasi oleh tim legal pada 24 Okt 2023.
                </p>
                <a href="#" class="inline-flex items-center gap-1.5 text-[#FDD835] hover:text-yellow-300 font-bold text-[14px] transition-colors">
                    Lihat Dokumen Legal <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                </a>
            </div>
        </div>
    </div>
</div>

    {{-- Modal Kembalikan ke Penyedia --}}
    <div id="modalKembalikan" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-8 mx-4 border border-slate-100">
            <h3 class="text-xl font-extrabold text-[#0f4c81] mb-2 flex items-center gap-2">
                <span class="material-symbols-outlined text-[#FDD835]">assignment_return</span>
                Kembalikan ke Penyedia
            </h3>
            <p class="text-sm text-slate-500 mb-6 leading-relaxed">Berikan catatan revisi kepada penyedia agar mereka dapat memperbaiki rincian teknis proyek ini dengan tepat.</p>
            
            <form action="{{ route('proyek.kembalikan', $proyek->id) }}" method="POST">
                @csrf
                @method('PATCH')
                
                <div class="mb-8">
                    <label class="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-2.5">Catatan Revisi</label>
                    <textarea name="catatan_revisi" rows="5" placeholder="Contoh: Tolong revisi bagian anggaran karena terlalu tinggi..." class="w-full bg-[#f8fafc] border border-slate-200 rounded-xl px-5 py-4 text-[14px] text-slate-700 focus:outline-none focus:ring-2 focus:ring-[#0f4c81] font-medium placeholder-slate-400 resize-none" required></textarea>
                </div>
                
                <div class="flex gap-3 justify-end">
                    <button type="button" onclick="document.getElementById('modalKembalikan').classList.add('hidden')" class="px-6 py-3 text-slate-500 font-bold hover:bg-slate-50 rounded-xl border border-slate-200 transition-colors">Batal</button>
                    <button type="submit" class="px-6 py-3 bg-[#0f4c81] text-white font-bold rounded-xl hover:bg-[#0a3a64] shadow-md transition-colors">Kirim Revisi</button>
                </div>
            </form>
        </div>
    </div>
@endsection
