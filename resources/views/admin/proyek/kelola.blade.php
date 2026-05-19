@extends('layouts.admin')

@section('title', 'Kelola Proyek Energi')
@section('page_heading', 'Kelola Proyek Energi')

@section('breadcrumbs')
    <span>Dashboard</span>
    <span class="mx-1">›</span>
    <span>Proyek Energi</span>
    <span class="mx-1">›</span>
    <span class="font-semibold text-nt-navy">Kelola Proyek</span>
@endsection

@section('content')

@if(session('success'))
    <div class="mb-6 flex items-center gap-3 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
        <span class="material-symbols-outlined text-green-600 text-base">check_circle</span>
        {{ session('success') }}
    </div>
@endif

{{-- Header --}}
<div class="flex flex-wrap items-end justify-between gap-4 mb-6">
    <div>
        <p class="text-sm text-slate-500 font-body">Kelola seluruh proyek energi — publikasikan, edit, atau hapus proyek.</p>
    </div>
    <a href="{{ route('proyek.create') }}"
       class="inline-flex items-center gap-2 rounded-lg bg-primary-container px-5 py-2.5 text-sm font-semibold text-on-primary-container shadow-sm hover:opacity-90 transition-all font-headline">
        <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 1">add_circle</span>
        Buat Proyek Baru
    </a>
</div>

{{-- Filter Bar --}}
<form method="GET" action="{{ route('proyek.kelola') }}"
      class="bg-white rounded-xl border border-slate-200 p-4 mb-6 flex flex-wrap gap-4 items-center">

    <div class="flex-1 min-w-[220px] relative">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari judul atau nama desa..."
               class="w-full border border-slate-200 rounded-lg pl-9 pr-4 py-2.5 text-sm font-body text-on-surface focus:outline-none focus:ring-2 focus:ring-secondary bg-surface-container-highest" />
    </div>

    <div class="relative min-w-[180px]">
        <select name="jenis_energi"
                class="w-full appearance-none border border-slate-200 bg-surface-container-highest text-on-surface font-body text-sm py-2.5 pl-4 pr-10 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary cursor-pointer">
            <option value="">Semua Jenis Energi</option>
            <option value="panel_surya"          {{ request('jenis_energi') === 'panel_surya'          ? 'selected' : '' }}>Panel Surya</option>
            <option value="mikro_hidro"          {{ request('jenis_energi') === 'mikro_hidro'          ? 'selected' : '' }}>Mikrohidro</option>
            <option value="biogas"               {{ request('jenis_energi') === 'biogas'               ? 'selected' : '' }}>Biogas</option>
            <option value="hybrid_solar_baterai" {{ request('jenis_energi') === 'hybrid_solar_baterai' ? 'selected' : '' }}>Hybrid Solar + Baterai</option>
        </select>
        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[18px]">expand_more</span>
    </div>

    <div class="relative min-w-[200px]">
        <select name="status"
                class="w-full appearance-none border border-slate-200 bg-surface-container-highest text-on-surface font-body text-sm py-2.5 pl-4 pr-10 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary cursor-pointer">
            <option value="">Semua Status</option>
            <option value="draft"                          {{ request('status') === 'draft'                          ? 'selected' : '' }}>Draft</option>
            <option value="menunggu_konfirmasi_penyedia"   {{ request('status') === 'menunggu_konfirmasi_penyedia'   ? 'selected' : '' }}>Menunggu Konfirmasi</option>
            <option value="diterima_penyedia"              {{ request('status') === 'diterima_penyedia'              ? 'selected' : '' }}>Diterima Penyedia</option>
            <option value="menunggu_review_admin"          {{ request('status') === 'menunggu_review_admin'          ? 'selected' : '' }}>Menunggu Review Admin</option>
            <option value="aktif_funding"                  {{ request('status') === 'aktif_funding'                  ? 'selected' : '' }}>Aktif Funding</option>
            <option value="eksekusi"                       {{ request('status') === 'eksekusi'                       ? 'selected' : '' }}>Eksekusi</option>
            <option value="selesai"                        {{ request('status') === 'selesai'                        ? 'selected' : '' }}>Selesai</option>
            <option value="ditolak"                        {{ request('status') === 'ditolak'                        ? 'selected' : '' }}>Ditolak</option>
        </select>
        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[18px]">expand_more</span>
    </div>

    <button type="submit" class="px-5 py-2.5 bg-secondary text-white text-sm font-semibold rounded-lg hover:opacity-90 transition-all">
        Filter
    </button>
    @if(request()->hasAny(['search', 'jenis_energi', 'status']))
        <a href="{{ route('proyek.kelola') }}" class="px-4 py-2.5 text-sm text-slate-500 hover:text-slate-700 font-medium">Reset</a>
    @endif
</form>

{{-- Table --}}
<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <table class="w-full text-left">
        <thead>
            <tr class="bg-surface-container-low border-b border-slate-200 text-slate-500 text-sm font-headline">
                <th class="py-3.5 px-6 font-semibold w-14">No</th>
                <th class="py-3.5 px-6 font-semibold">Judul Proyek</th>
                <th class="py-3.5 px-6 font-semibold">Desa</th>
                <th class="py-3.5 px-6 font-semibold">Jenis Energi</th>
                <th class="py-3.5 px-6 font-semibold">Status</th>
                <th class="py-3.5 px-6 font-semibold text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="text-sm font-body text-on-surface divide-y divide-slate-100">
            @forelse($proyeks as $proyek)
            @php
                $jenisLabel = match($proyek->jenis_energi) {
                    'panel_surya'          => ['label' => 'Panel Surya',          'icon' => 'solar_power',         'class' => 'bg-amber-50 text-amber-700'],
                    'mikro_hidro'          => ['label' => 'Mikrohidro',           'icon' => 'water_drop',          'class' => 'bg-blue-50 text-blue-700'],
                    'biogas'               => ['label' => 'Biogas',               'icon' => 'eco',                 'class' => 'bg-green-50 text-green-700'],
                    'hybrid_solar_baterai' => ['label' => 'Hybrid Solar+Baterai', 'icon' => 'bolt',                'class' => 'bg-purple-50 text-purple-700'],
                    default                => ['label' => $proyek->jenis_energi,  'icon' => 'energy_savings_leaf', 'class' => 'bg-slate-100 text-slate-600'],
                };
                $statusLabel = match($proyek->status) {
                    'draft'                       => ['label' => 'Draft',               'class' => 'bg-slate-100 text-slate-600',                          'dot' => 'bg-slate-400'],
                    'menunggu_konfirmasi_penyedia' => ['label' => 'Menunggu Konfirmasi', 'class' => 'bg-amber-50 text-amber-700',                           'dot' => 'bg-amber-400'],
                    'diterima_penyedia'            => ['label' => 'Diterima Penyedia',  'class' => 'bg-blue-50 text-blue-700',                             'dot' => 'bg-blue-400'],
                    'menunggu_review_admin'        => ['label' => 'Menunggu Review',    'class' => 'bg-purple-50 text-purple-700',                         'dot' => 'bg-purple-400'],
                    'aktif_funding'                => ['label' => 'Aktif Funding',      'class' => 'bg-tertiary-container/30 text-on-tertiary-container',  'dot' => 'bg-tertiary'],
                    'eksekusi'                     => ['label' => 'Eksekusi',           'class' => 'bg-teal-50 text-teal-700',                             'dot' => 'bg-teal-400'],
                    'selesai'                      => ['label' => 'Selesai',            'class' => 'bg-emerald-50 text-emerald-700',                       'dot' => 'bg-emerald-400'],
                    'refund'                       => ['label' => 'Refund',             'class' => 'bg-red-50 text-red-700',                               'dot' => 'bg-red-400'],
                    'ditolak'                      => ['label' => 'Ditolak',            'class' => 'bg-red-50 text-red-700',                               'dot' => 'bg-red-400'],
                    default                        => ['label' => $proyek->status,      'class' => 'bg-slate-100 text-slate-500',                          'dot' => 'bg-slate-400'],
                };
                $canTogglePublish = !in_array($proyek->status, ['eksekusi', 'selesai', 'refund']);
                $canDelete        = $proyek->status === 'draft';
            @endphp
            <tr class="hover:bg-surface-container-low/50 transition-colors">
                <td class="py-4 px-6 text-slate-400">{{ $proyeks->firstItem() + $loop->index }}</td>
                <td class="py-4 px-6 font-medium max-w-[220px] truncate">{{ $proyek->judul }}</td>
                <td class="py-4 px-6 text-slate-500">
                    {{ $proyek->desa->nama_desa ?? '—' }}
                    @if($proyek->desa?->provinsi)
                        <span class="text-slate-400 text-xs">, {{ $proyek->desa->provinsi }}</span>
                    @endif
                </td>
                <td class="py-4 px-6">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded text-xs font-semibold {{ $jenisLabel['class'] }}">
                        <span class="material-symbols-outlined text-[13px]">{{ $jenisLabel['icon'] }}</span>
                        {{ $jenisLabel['label'] }}
                    </span>
                </td>
                <td class="py-4 px-6">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded text-xs font-semibold {{ $statusLabel['class'] }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $statusLabel['dot'] }}"></span>
                        {{ $statusLabel['label'] }}
                    </span>
                </td>
                <td class="py-4 px-6 text-right">
                    <div class="flex items-center justify-end gap-1">
                        {{-- View detail --}}
                        <a href="{{ route('proyek.review', $proyek->id) }}"
                           class="p-1.5 text-secondary hover:bg-secondary/10 rounded transition-colors"
                           title="Lihat Detail">
                            <span class="material-symbols-outlined text-sm">visibility</span>
                        </a>

                        {{-- Edit --}}
                        <a href="{{ route('proyek.create', ['draft_id' => $proyek->id]) }}"
                           class="p-1.5 text-secondary hover:bg-secondary/10 rounded transition-colors"
                           title="Edit">
                            <span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1">edit</span>
                        </a>

                        {{-- Publish toggle --}}
                        @if($canTogglePublish)
                            <button type="button"
                                    onclick="openPublishModal({{ $proyek->id }}, '{{ addslashes($proyek->judul) }}', '{{ $proyek->status }}')"
                                    class="p-1.5 rounded transition-colors {{ $proyek->status === 'aktif_funding' ? 'text-slate-400 hover:bg-red-50 hover:text-error' : 'text-slate-400 hover:bg-tertiary-container/30 hover:text-tertiary' }}"
                                    title="{{ $proyek->status === 'aktif_funding' ? 'Batalkan Publikasi' : 'Publikasikan' }}">
                                <span class="material-symbols-outlined text-sm">{{ $proyek->status === 'aktif_funding' ? 'unpublished' : 'publish' }}</span>
                            </button>
                        @else
                            <span class="p-1.5 text-slate-200 cursor-not-allowed" title="Tidak dapat diubah">
                                <span class="material-symbols-outlined text-sm">lock</span>
                            </span>
                        @endif

                        {{-- Delete --}}
                        @if($canDelete)
                            <button type="button"
                                    onclick="openDeleteModal({{ $proyek->id }}, '{{ addslashes($proyek->judul) }}')"
                                    class="p-1.5 text-slate-400 hover:bg-red-50 hover:text-error rounded transition-colors"
                                    title="Hapus">
                                <span class="material-symbols-outlined text-sm">delete</span>
                            </button>
                        @else
                            <span class="p-1.5 text-slate-200 cursor-not-allowed" title="Hanya draft yang dapat dihapus">
                                <span class="material-symbols-outlined text-sm">delete</span>
                            </span>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="py-16 text-center text-slate-400 font-body">
                    <span class="material-symbols-outlined text-5xl block mb-3">bolt</span>
                    Tidak ada proyek ditemukan.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between">
        <span class="text-sm text-slate-400 font-body">
            Menampilkan {{ $proyeks->firstItem() ?? 0 }}–{{ $proyeks->lastItem() ?? 0 }}
            dari {{ $proyeks->total() }} proyek
        </span>
        {{ $proyeks->links() }}
    </div>
</div>

{{-- Publish Modal --}}
<div id="publishModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 p-8">
        <div class="flex items-center gap-3 mb-4">
            <span id="publishModalIcon" class="material-symbols-outlined text-3xl text-tertiary">publish</span>
            <h3 class="text-lg font-bold font-headline text-on-surface" id="publishModalTitle">Publikasikan Proyek?</h3>
        </div>
        <p class="text-sm text-slate-500 font-body mb-6" id="publishModalDesc">
            Proyek akan dipublikasikan dan tampil di halaman publik.
        </p>
        <div class="flex items-center justify-end gap-3">
            <button onclick="closePublishModal()"
                    class="px-5 py-2 text-sm font-semibold text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors">
                Batal
            </button>
            <form id="publishForm" method="POST">
                @csrf @method('PATCH')
                <button type="submit"
                        id="publishBtn"
                        class="px-5 py-2 text-sm font-semibold bg-tertiary text-white rounded-lg hover:opacity-90 transition-colors">
                    Publikasikan
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Delete Modal --}}
<div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 p-8">
        <div class="flex items-center gap-3 mb-4">
            <span class="material-symbols-outlined text-3xl text-error">delete_forever</span>
            <h3 class="text-lg font-bold font-headline text-on-surface">Hapus Proyek?</h3>
        </div>
        <p class="text-sm text-slate-500 font-body mb-6" id="deleteModalDesc">
            Proyek ini akan dihapus permanen beserta semua foto. Tindakan ini tidak dapat dibatalkan.
        </p>
        <div class="flex items-center justify-end gap-3">
            <button onclick="closeDeleteModal()"
                    class="px-5 py-2 text-sm font-semibold text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors">
                Batal
            </button>
            <form id="deleteForm" method="POST">
                @csrf @method('DELETE')
                <button type="submit"
                        class="px-5 py-2 text-sm font-semibold bg-error text-white rounded-lg hover:opacity-90 transition-colors">
                    Hapus Permanen
                </button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openPublishModal(id, judul, status) {
    const isAktif = status === 'aktif_funding';
    document.getElementById('publishForm').action = `/admin/proyek/${id}/publish`;
    document.getElementById('publishModalIcon').textContent = isAktif ? 'unpublished' : 'publish';
    document.getElementById('publishModalIcon').className = `material-symbols-outlined text-3xl ${isAktif ? 'text-error' : 'text-tertiary'}`;
    document.getElementById('publishModalTitle').textContent = isAktif ? 'Batalkan Publikasi?' : 'Publikasikan Proyek?';
    document.getElementById('publishModalDesc').textContent = isAktif
        ? `"${judul}" akan dinonaktifkan dari halaman publik.`
        : `"${judul}" akan dipublikasikan dan tampil di halaman publik.`;
    document.getElementById('publishBtn').textContent = isAktif ? 'Batalkan Publikasi' : 'Publikasikan';
    document.getElementById('publishBtn').className = `px-5 py-2 text-sm font-semibold rounded-lg hover:opacity-90 transition-colors ${isAktif ? 'bg-error text-white' : 'bg-tertiary text-white'}`;
    document.getElementById('publishModal').classList.replace('hidden', 'flex');
}

function closePublishModal() {
    document.getElementById('publishModal').classList.replace('flex', 'hidden');
}

function openDeleteModal(id, judul) {
    document.getElementById('deleteForm').action = `/admin/proyek/${id}`;
    document.getElementById('deleteModalDesc').textContent = `"${judul}" akan dihapus permanen beserta semua foto. Tindakan ini tidak dapat dibatalkan.`;
    document.getElementById('deleteModal').classList.replace('hidden', 'flex');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.replace('flex', 'hidden');
}

document.getElementById('publishModal').addEventListener('click', function(e) {
    if (e.target === this) closePublishModal();
});

document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
</script>
@endpush
