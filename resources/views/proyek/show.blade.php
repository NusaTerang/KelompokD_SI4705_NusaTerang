@extends('layouts.app')

@section('content')

@php
    $progress  = $proyek->target_dana > 0 ? round(($proyek->dana_terkumpul / $proyek->target_dana) * 100) : 0;
    $daysLeft  = $proyek->estimasi_selesai ? max(0, (int) ceil(now()->diffInDays($proyek->estimasi_selesai, false))) : 0;
    $firstFoto = $proyek->fotos->first();
    $imageUrl  = $firstFoto
        ? (str_starts_with($firstFoto->path, 'http') ? $firstFoto->path : asset('storage/' . $firstFoto->path))
        : asset('images/default-project.svg');
    $location  = $proyek->desa
        ? $proyek->desa->nama_desa . ', ' . $proyek->desa->provinsi
        : 'Lokasi tidak diketahui';

    $statusMap = [
        'draft'                          => ['text' => 'DRAFT',              'bg' => 'bg-surface-container',    'color' => 'text-on-surface-variant'],
        'menunggu_konfirmasi_penyedia'   => ['text' => 'MENUNGGU PENYEDIA',  'bg' => 'bg-yellow-100',           'color' => 'text-yellow-800'],
        'menunggu_review_admin'          => ['text' => 'MENUNGGU REVIEW',    'bg' => 'bg-yellow-100',           'color' => 'text-yellow-800'],
        'aktif_funding'                  => ['text' => 'SEDANG BERJALAN',    'bg' => 'bg-primary-container',    'color' => 'text-on-primary-fixed'],
        'eksekusi'                       => ['text' => 'DALAM EKSEKUSI',     'bg' => 'bg-secondary-container',  'color' => 'text-on-secondary-fixed'],
        'selesai'                        => ['text' => 'SELESAI',            'bg' => 'bg-tertiary-container',   'color' => 'text-on-tertiary-fixed'],
        'ditolak'                        => ['text' => 'DITOLAK',            'bg' => 'bg-error-container',      'color' => 'text-on-error-container'],
    ];
    $status = $statusMap[$proyek->status] ?? ['text' => strtoupper($proyek->status), 'bg' => 'bg-surface-container', 'color' => 'text-on-surface-variant'];

    if (!function_exists('formatRupiahShort')) {
        function formatRupiahShort($amount) {
            if ($amount >= 1000000) {
                return number_format($amount / 1000000, 1, ',', '.') . 'jt';
            } elseif ($amount >= 1000) {
                return number_format($amount / 1000, 0, ',', '.') . 'rb';
            }
            return number_format($amount, 0, ',', '.');
        }
    }
@endphp

<div class="w-full max-w-[1216px] mx-auto px-4 py-12 flex flex-col lg:flex-row gap-12 items-start">

    {{-- Left Column --}}
    <div class="w-full lg:flex-1 flex flex-col gap-8">

        {{-- Hero Image --}}
        <div class="w-full relative rounded-xl overflow-hidden shadow-md h-[300px]">
            <img src="{{ $imageUrl }}" alt="{{ $proyek->judul }}" class="w-full h-full object-cover" />
            <div class="absolute top-6 left-6 {{ $status['bg'] }} px-4 py-1.5 rounded-full shadow-sm">
                <span class="{{ $status['color'] }} text-xs font-bold font-headline tracking-wider uppercase">
                    {{ $status['text'] }}
                </span>
            </div>
        </div>

        {{-- Title & Location --}}
        <div class="flex flex-col gap-3">
            <h1 class="text-on-surface text-3xl md:text-4xl font-headline font-semibold leading-tight">
                {{ $proyek->judul }}
            </h1>
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px] text-on-surface-variant">location_on</span>
                <span class="text-on-surface-variant text-base font-medium">{{ $location }}</span>
            </div>
        </div>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 w-full">
            {{-- Dana Terkumpul --}}
            <div class="bg-surface-container-low p-4 rounded-xl border-l-4 border-primary flex flex-col gap-1">
                <p class="text-on-surface-variant text-xs font-bold uppercase tracking-wide">
                    TERKUMPUL
                </p>

                <p id="stats-funding"
                class="text-on-surface text-lg font-bold">
                    Rp {{ number_format($proyek->dana_terkumpul / 1000000, 1) }}jt
                </p>
            </div>

            {{-- Target --}}
            <div class="bg-surface-container-low p-4 rounded-xl border-l-4 border-outline-variant flex flex-col gap-1">
                <p class="text-on-surface-variant text-xs font-bold uppercase tracking-wide">
                    TARGET
                </p>

                <p class="text-on-surface text-lg font-bold">
                    Rp {{ number_format($proyek->target_dana / 1000000, 1) }}jt
                </p>
            </div>

            {{-- Donatur --}}
            <div class="bg-surface-container-low p-4 rounded-xl border-l-4 border-secondary-container flex flex-col gap-1">
                <p class="text-on-surface-variant text-xs font-bold uppercase tracking-wide">
                    DONATUR
                </p>

                <p id="donatur-count"
                class="text-on-surface text-lg font-bold">
                    {{ $proyek->donasis?->count() ?? 0 }}
                </p>
            </div>

            {{-- Sisa Hari --}}
            <div class="bg-surface-container-low p-4 rounded-xl border-l-4 border-tertiary-container flex flex-col gap-1">
                <p class="text-on-surface-variant text-xs font-bold uppercase tracking-wide">
                    SISA HARI
                </p>

                <p class="text-on-surface text-lg font-bold">
                    {{ $daysLeft }} Hari
                </p>
            </div>

        </div>

        {{-- Progress Bar --}}
        <div class="flex flex-col gap-2 w-full">

            <div class="flex justify-between items-end">
                <p class="text-on-surface-variant text-sm font-bold">
                    Kemajuan Pendanaan
                </p>

                <p id="progress-text"
                class="text-primary text-2xl font-bold">
                    {{ min($progress, 100) }}%
                </p>
            </div>

            <div class="w-full h-4 bg-surface-container rounded-full overflow-hidden">

                <div id="progress-bar"
                    class="h-full solar-gradient rounded-full transition-all duration-500"
                    style="width: {{ min($progress, 100) }}%">
                </div>

            </div>

        </div>

        {{-- About --}}
        <div class="flex flex-col gap-4 w-full">
            <h2 class="text-secondary text-2xl font-headline font-semibold">Tentang Proyek</h2>
            @if($proyek->deskripsi)
                <div class="text-on-surface-variant text-lg leading-relaxed whitespace-pre-line">{{ $proyek->deskripsi }}</div>
            @else
                <p class="text-on-surface-variant italic">Belum ada deskripsi proyek.</p>
            @endif
        </div>

        {{-- Photo Gallery --}}
        @if($proyek->fotos->count() > 1)
            <div class="flex flex-col gap-4 w-full">
                <h2 class="text-secondary text-2xl font-headline font-semibold">Galeri</h2>
                <div class="flex gap-4 overflow-x-auto pb-2 hide-scrollbar">
                    @foreach($proyek->fotos as $foto)
                        <img
                            src="{{ str_starts_with($foto->path, 'http') ? $foto->path : asset('storage/' . $foto->path) }}"
                            alt="Foto proyek"
                            class="h-40 w-64 rounded-xl object-cover shrink-0 border border-surface-container"
                        />
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Project Info Details --}}
        <div class="flex flex-col gap-4 w-full">
            <h2 class="text-secondary text-2xl font-headline font-semibold">Informasi Proyek</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-surface-container-low p-4 rounded-xl flex flex-col gap-1">
                    <p class="text-on-surface-variant text-xs font-bold uppercase tracking-wide">JENIS ENERGI</p>
                    <p class="text-on-surface font-semibold capitalize">{{ str_replace('_', ' ', $proyek->jenis_energi ?? '-') }}</p>
                </div>
                <div class="bg-surface-container-low p-4 rounded-xl flex flex-col gap-1">
                    <p class="text-on-surface-variant text-xs font-bold uppercase tracking-wide">PENYEDIA</p>
                    <p class="text-on-surface font-semibold">{{ $proyek->penyedia ? $proyek->penyedia->nama : 'Belum ditentukan' }}</p>
                </div>
                @if($proyek->estimasi_mulai)
                    <div class="bg-surface-container-low p-4 rounded-xl flex flex-col gap-1">
                        <p class="text-on-surface-variant text-xs font-bold uppercase tracking-wide">ESTIMASI MULAI</p>
                        <p class="text-on-surface font-semibold">{{ $proyek->estimasi_mulai->format('d M Y') }}</p>
                    </div>
                @endif
                @if($proyek->estimasi_selesai)
                    <div class="bg-surface-container-low p-4 rounded-xl flex flex-col gap-1">
                        <p class="text-on-surface-variant text-xs font-bold uppercase tracking-wide">ESTIMASI SELESAI</p>
                        <p class="text-on-surface font-semibold">{{ $proyek->estimasi_selesai->format('d M Y') }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Rincian Anggaran --}}
        @if(!empty($costBreakdown))
            <div class="flex flex-col gap-4 w-full">
                <h2 class="text-secondary text-2xl font-headline font-semibold">Rincian Anggaran</h2>
                <div class="bg-surface-container-lowest border border-surface-container rounded-xl overflow-hidden">
                    <div class="divide-y divide-surface-container">
                        @php
                            $totalVendor = (float) ($proyek->penugasan->first()?->detail?->target_dana ?? 0);
                        @endphp
                        @foreach($costBreakdown as $item)
                            <div class="flex justify-between items-center p-4 hover:bg-surface-container-low transition-colors">
                                <span class="text-on-surface font-medium">{{ $item['nama'] }}</span>
                                <span class="text-on-surface-variant font-bold">Rp {{ formatRupiahShort($item['nominal']) }}</span>
                            </div>
                        @endforeach
                        
                        {{-- Platform Fee --}}
                        @php
                            $platformFee = $totalVendor * 0.05;
                            $grandTotal = $totalVendor + $platformFee;
                        @endphp
                        <div class="flex justify-between items-center p-4 bg-surface-container-low">
                            <span class="text-on-surface font-medium flex items-center gap-2">
                                <span class="material-symbols-outlined text-[16px] text-tertiary">info</span>
                                Total Platform Fee (5%)
                            </span>
                            <span class="text-on-surface-variant font-bold">Rp {{ formatRupiahShort($platformFee) }}</span>
                        </div>
                        
                        {{-- Grand Total --}}
                        <div class="flex justify-between items-center p-5 bg-surface-container border-t-2 border-primary/20">
                            <span class="text-on-surface font-bold text-lg">Total Kebutuhan Dana</span>
                            <span class="text-primary font-bold text-xl">Rp {{ formatRupiahShort($grandTotal) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Timeline Placeholder --}}
        {{-- Activity Feed --}}
        <div class="flex flex-col gap-6 w-full py-6">

            <div class="flex items-center justify-between">
                <h2 class="text-secondary text-2xl font-headline font-semibold">
                    Aktivitas Donasi Terbaru
                </h2>

                <span class="text-sm text-on-surface-variant">
                    Real-time update
                </span>
            </div>

            <div id="activity-feed"
                class="flex flex-col gap-4">

                @if(isset($proyek->donasis) && $proyek->donasis->count() > 0)

                    @foreach($proyek->donasis->take(5) as $donasi)

                        <div class="bg-surface-container-low p-5 rounded-xl border border-surface-container">

                            <div class="flex items-center justify-between">

                                <div class="flex flex-col gap-1">

                                    <p class="text-on-surface font-semibold">
                                        {{ $donasi->user->name ?? 'Donatur' }}
                                    </p>

                                    <p class="text-sm text-on-surface-variant">
                                        Berdonasi sebesar
                                        <span class="font-bold">
                                            Rp {{ number_format($donasi->nominal, 0, ',', '.') }}
                                        </span>
                                    </p>

                                </div>

                                <span class="text-xs text-on-surface-variant">
                                    {{ $donasi->created_at->diffForHumans() }}
                                </span>

                            </div>

                        </div>

                    @endforeach

                @else

                    <div id="empty-state"
                        class="flex flex-col items-center justify-center py-12 bg-surface-container-low rounded-xl">

                        <span class="material-symbols-outlined text-[48px] text-outline-variant mb-3">
                            volunteer_activism
                        </span>

                        <p class="text-on-surface-variant text-sm">
                            Belum ada donasi pada proyek ini.
                        </p>

                    </div>

                @endif

            </div>

        </div>

    </div>

    {{-- Right Column - Donation Card --}}
    <div class="w-full lg:w-[420px] flex flex-col sticky top-24 shrink-0">
        <div class="bg-white rounded-2xl p-8 border border-surface-container shadow-xl flex flex-col gap-6 w-full">

            <h2 class="text-secondary text-2xl font-headline font-semibold">Dukung Proyek Ini</h2>

            <div class="flex flex-col gap-2">
                <div class="flex justify-between items-center">
                    <p class="text-on-surface-variant text-xs font-bold uppercase">TERKUMPUL</p>
                    <p id="funding-amount" class="text-tertiary text-2xl font-bold">Rp {{ number_format($proyek->dana_terkumpul, 0, ',', '.') }}</p>
                </div>
                <div class="w-full h-2 bg-surface-container rounded-full overflow-hidden my-1">

                    <div id="side-progress-bar"
                        class="h-full solar-gradient rounded-full transition-all duration-500"
                        style="width: {{ min($progress, 100) }}%">
                    </div>

                </div>
                <div class="flex gap-1 text-xs">
                    <span class="text-on-surface-variant">Dari target</span>
                    <span class="text-on-surface font-bold">Rp {{ number_format($proyek->target_dana, 0, ',', '.') }}</span>
                </div>
            </div>

            @if($proyek->status === 'eksekusi')
                <button
                    disabled
                    class="w-full bg-gray-300 text-gray-600 font-headline font-extrabold text-lg py-4 rounded-xl cursor-not-allowed text-center block">
                    Pendanaan Tercapai
                </button>
            @auth
            @else
                <form action="{{ route('donasi.store', $proyek->id) }}" method="POST" class="flex flex-col gap-4 w-full">
                    @csrf
                    <div class="grid grid-cols-2 gap-3 w-full">
                        <button type="button" onclick="document.getElementById('amount').value=50000" class="py-3 rounded-xl border border-outline-variant text-on-surface font-bold hover:bg-surface-container-low transition-colors">Rp 50k</button>
                        <button type="button" onclick="document.getElementById('amount').value=100000" class="py-3 rounded-xl border border-outline-variant text-on-surface font-bold hover:bg-surface-container-low transition-colors">Rp 100k</button>
                        <button type="button" onclick="document.getElementById('amount').value=250000" class="py-3 rounded-xl border border-outline-variant text-on-surface font-bold hover:bg-surface-container-low transition-colors">Rp 250k</button>
                        <button type="button" onclick="document.getElementById('amount').value=500000" class="py-3 rounded-xl border border-outline-variant text-on-surface font-bold hover:bg-surface-container-low transition-colors">Rp 500k</button>
                    </div>

                    <div class="flex flex-col gap-2 w-full">
                        <label class="text-on-surface-variant text-sm font-bold">Nominal Donasi Lainnya</label>
                        <div class="relative w-full">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant font-bold text-sm">Rp</span>
                            <input
                                id="amount"
                                name="nominal"
                                type="text"
                                placeholder="0"
                                class="w-full bg-surface-container-low rounded-xl py-4 pl-12 pr-4 outline-none text-right font-bold text-on-surface focus:ring-2 focus:ring-secondary border-none"
                            />
                        </div>
                    </div>

                    <button
                        id="donasi-button"
                        type="submit"
                        class="w-full bg-primary-container text-on-primary-fixed font-headline font-extrabold text-lg py-4 rounded-xl shadow-md hover:opacity-90 transition-all mt-2">
                        Donasi Sekarang
                    </button>
                </form>
            @else
                <div class="grid grid-cols-2 gap-3 w-full opacity-50 pointer-events-none">
                    <button class="py-3 rounded-xl border border-outline-variant text-on-surface font-bold">Rp 50k</button>
                    <button class="py-3 rounded-xl border border-outline-variant text-on-surface font-bold">Rp 100k</button>
                    <button class="py-3 rounded-xl border border-outline-variant text-on-surface font-bold">Rp 250k</button>
                    <button class="py-3 rounded-xl border border-outline-variant text-on-surface font-bold">Rp 500k</button>
                </div>

                <div class="flex flex-col gap-2 w-full opacity-50 pointer-events-none">
                    <label class="text-on-surface-variant text-sm font-bold">Nominal Donasi Lainnya</label>
                    <div class="relative w-full">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant font-bold text-sm">Rp</span>
                        <input type="text" placeholder="0" class="w-full bg-surface-container-low rounded-xl py-4 pl-12 pr-4 outline-none text-right font-bold text-on-surface border-none" disabled />
                    </div>
                </div>

                <a href="{{ route('login') }}" class="w-full bg-primary-container text-on-primary-fixed font-headline font-extrabold text-lg py-4 rounded-xl shadow-md hover:opacity-90 transition-all mt-2 text-center block">
                    Login untuk Donasi
                </a>
            @endauth
            @endif

            <div class="flex items-center gap-6 pt-2 border-t border-surface-container">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[16px] text-tertiary">verified_user</span>
                    <span class="text-[10px] font-bold text-on-surface-variant uppercase">TRANSAKSI AMAN</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[16px] text-tertiary">eco</span>
                    <span class="text-[10px] font-bold text-on-surface-variant uppercase">100% BERKELANJUTAN</span>
                </div>
            </div>

        </div>
    </div>

</div>

<script>

async function refreshFundingData()
{
    try {

        const response = await fetch(
            '/api/v1/proyek/{{ $proyek->id }}/funding',
            {
                headers: {
                    'Accept': 'application/json'
                }
            }
        );

        const data = await response.json();

        // Funding
        document.getElementById('funding-amount').innerText =
            'Rp ' + Number(data.dana_terkumpul)
                .toLocaleString('id-ID');

        // Stats Funding
        document.getElementById('stats-funding').innerText =
            'Rp ' +
            (data.dana_terkumpul / 1000000).toFixed(1)
            + 'jt';

        // Donatur
        document.getElementById('donatur-count').innerText =
            data.jumlah_donatur;

        // Progress Text
        document.getElementById('progress-text').innerText =
            data.persentase + '%';

        // Main Progress Bar
        document.getElementById('progress-bar').style.width =
            data.persentase + '%';

        // Side Progress Bar
        document.getElementById('side-progress-bar').style.width =
            data.persentase + '%';

        // Auto Disable Button
        if (data.status === 'eksekusi') {

            const button = document.getElementById('donasi-button');

            if (button) {

                button.disabled = true;

                button.innerText = 'Pendanaan Tercapai';

                button.classList.remove(
                    'bg-primary-container'
                );

                button.classList.add(
                    'bg-gray-300',
                    'text-gray-600',
                    'cursor-not-allowed'
                );
            }
        }

        // Activity Feed
        const activityFeed =
            document.getElementById('activity-feed');

        if (data.donasi_terbaru.length > 0) {

            activityFeed.innerHTML = '';

            data.donasi_terbaru.forEach((donasi) => {

                activityFeed.innerHTML += `
                    <div class="bg-surface-container-low p-5 rounded-xl border border-surface-container">

                        <div class="flex items-center justify-between">

                            <div class="flex flex-col gap-1">

                                <p class="text-on-surface font-semibold">
                                    ${donasi.user?.name ?? 'Donatur'}
                                </p>

                                <p class="text-sm text-on-surface-variant">
                                    Berdonasi sebesar
                                    <span class="font-bold">
                                        Rp ${Number(donasi.nominal)
                                            .toLocaleString('id-ID')}
                                    </span>
                                </p>

                            </div>

                        </div>

                    </div>
                `;
            });
        }

    } catch (error) {

        console.error(
            'Funding realtime error:',
            error
        );
    }
}

<script>

async function refreshFundingData()
{
    try {

        const response = await fetch(
            '/api/v1/proyek/{{ $proyek->id }}/funding',
            {
                headers: {
                    'Accept': 'application/json'
                }
            }
        );

        const data = await response.json();

        // Funding
        document.getElementById('funding-amount').innerText =
            'Rp ' + Number(data.dana_terkumpul)
                .toLocaleString('id-ID');

        // Stats Funding
        document.getElementById('stats-funding').innerText =
            'Rp ' +
            (data.dana_terkumpul / 1000000).toFixed(1)
            + 'jt';

        // Donatur
        document.getElementById('donatur-count').innerText =
            data.jumlah_donatur;

        // Progress Text
        document.getElementById('progress-text').innerText =
            data.persentase + '%';

        // Main Progress Bar
        document.getElementById('progress-bar').style.width =
            data.persentase + '%';

        // Side Progress Bar
        document.getElementById('side-progress-bar').style.width =
            data.persentase + '%';

        // Auto Disable Button
        if (data.status === 'eksekusi') {

            const button = document.getElementById('donasi-button');

            if (button) {

                button.disabled = true;

                button.innerText = 'Pendanaan Tercapai';

                button.classList.remove(
                    'bg-primary-container'
                );

                button.classList.add(
                    'bg-gray-300',
                    'text-gray-600',
                    'cursor-not-allowed'
                );
            }
        }

        // Activity Feed
        const activityFeed =
            document.getElementById('activity-feed');

        if (data.donasi_terbaru.length > 0) {

            activityFeed.innerHTML = '';

            data.donasi_terbaru.forEach((donasi) => {

                activityFeed.innerHTML += `
                    <div class="bg-surface-container-low p-5 rounded-xl border border-surface-container">

                        <div class="flex items-center justify-between">

                            <div class="flex flex-col gap-1">

                                <p class="text-on-surface font-semibold">
                                    ${donasi.user?.name ?? 'Donatur'}
                                </p>

                                <p class="text-sm text-on-surface-variant">
                                    Berdonasi sebesar
                                    <span class="font-bold">
                                        Rp ${Number(donasi.nominal)
                                            .toLocaleString('id-ID')}
                                    </span>
                                </p>

                            </div>

                        </div>

                    </div>
                `;
            });
        }

    } catch (error) {

        console.error(
            'Funding realtime error:',
            error
        );
    }
}

// Refresh tiap 5 detik
setInterval(refreshFundingData, 5000);

</script>

@endsection
