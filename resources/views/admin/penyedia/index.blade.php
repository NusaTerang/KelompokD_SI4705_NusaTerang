@extends('layouts.admin')

@section('title', 'Management Penyedia Energi')
@section('page_heading', 'Management Penyedia Energi')

@section('breadcrumbs')
    <span>Dashboard</span>
    <span class="mx-1">›</span>
    <span class="font-semibold text-nt-navy">Penyedia Energi</span>
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
        <p class="text-sm text-slate-500 font-body">Kelola daftar vendor dan penyedia energi terbarukan.</p>
    </div>
    <a href="{{ route('admin.vendors.create') }}"
       class="inline-flex items-center gap-2 rounded-lg bg-primary-container px-5 py-2.5 text-sm font-semibold text-on-primary-container shadow-sm hover:opacity-90 transition-all font-headline">
        <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 1">add_circle</span>
        Tambah Penyedia Energi
    </a>
</div>

{{-- Filter Bar --}}
<form method="GET" action="{{ route('admin.vendors.index') }}"
      class="bg-white rounded-xl border border-slate-200 p-4 mb-6 flex flex-wrap gap-4 items-center">

    <div class="flex-1 min-w-[220px] relative">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari nama vendor..."
               class="w-full border border-slate-200 rounded-lg pl-9 pr-4 py-2.5 text-sm font-body text-on-surface focus:outline-none focus:ring-2 focus:ring-secondary bg-surface-container-highest" />
    </div>

    <div class="relative min-w-[180px]">
        <select name="spesialisasi"
                class="w-full appearance-none border border-slate-200 bg-surface-container-highest text-on-surface font-body text-sm py-2.5 pl-4 pr-10 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary cursor-pointer">
            <option value="">Semua Spesialisasi</option>
            <option value="solar"      {{ request('spesialisasi') === 'solar'      ? 'selected' : '' }}>Solar PV</option>
            <option value="mikro_hidro"{{ request('spesialisasi') === 'mikro_hidro'? 'selected' : '' }}>Mikro Hidro</option>
            <option value="lainnya"    {{ request('spesialisasi') === 'lainnya'    ? 'selected' : '' }}>Lainnya</option>
        </select>
        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[18px]">expand_more</span>
    </div>

    <div class="relative min-w-[180px]">
        <select name="provinsi"
                class="w-full appearance-none border border-slate-200 bg-surface-container-highest text-on-surface font-body text-sm py-2.5 pl-4 pr-10 rounded-lg focus:outline-none focus:ring-2 focus:ring-secondary cursor-pointer">
            <option value="">Semua Provinsi</option>
            @foreach($provinsiList as $prov)
                <option value="{{ $prov }}" {{ request('provinsi') === $prov ? 'selected' : '' }}>{{ $prov }}</option>
            @endforeach
        </select>
        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[18px]">expand_more</span>
    </div>

    <button type="submit" class="px-5 py-2.5 bg-secondary text-white text-sm font-semibold rounded-lg hover:opacity-90 transition-all">
        Filter
    </button>
    @if(request()->hasAny(['search','spesialisasi','provinsi']))
        <a href="{{ route('admin.vendors.index') }}" class="px-4 py-2.5 text-sm text-slate-500 hover:text-slate-700 font-medium">Reset</a>
    @endif
</form>

{{-- Table --}}
<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <table class="w-full text-left">
        <thead>
            <tr class="bg-surface-container-low border-b border-slate-200 text-slate-500 text-sm font-headline">
                <th class="py-3.5 px-6 font-semibold w-14">No</th>
                <th class="py-3.5 px-6 font-semibold">Nama Perusahaan</th>
                <th class="py-3.5 px-6 font-semibold">Spesialisasi</th>
                <th class="py-3.5 px-6 font-semibold">Provinsi / Kota</th>
                <th class="py-3.5 px-6 font-semibold">Status</th>
                <th class="py-3.5 px-6 font-semibold text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="text-sm font-body text-on-surface divide-y divide-slate-100">
            @forelse($vendors as $vendor)
            @php
                $spLabel = match($vendor->spesialisasi) {
                    'solar'      => ['label' => 'Solar PV', 'icon' => 'solar_power', 'class' => 'bg-amber-50 text-amber-700'],
                    'mikro_hidro'=> ['label' => 'Mikro Hidro', 'icon' => 'water_drop', 'class' => 'bg-blue-50 text-blue-700'],
                    default      => ['label' => 'Lainnya', 'icon' => 'air', 'class' => 'bg-slate-100 text-slate-600'],
                };
            @endphp
            <tr class="hover:bg-surface-container-low/50 transition-colors">
                <td class="py-4 px-6 text-slate-400">{{ $vendors->firstItem() + $loop->index }}</td>
                <td class="py-4 px-6 font-medium">{{ $vendor->nama }}</td>
                <td class="py-4 px-6">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded text-xs font-semibold {{ $spLabel['class'] }}">
                        <span class="material-symbols-outlined text-[13px]">{{ $spLabel['icon'] }}</span>
                        {{ $spLabel['label'] }}
                    </span>
                </td>
                <td class="py-4 px-6 text-slate-500">
                    {{ $vendor->provinsi_operasi }}{{ $vendor->kota ? ', ' . $vendor->kota : '' }}
                </td>
                <td class="py-4 px-6">
                    @if($vendor->status === 'aktif')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-tertiary-container/30 text-on-tertiary-container text-xs font-semibold">
                            <span class="w-1.5 h-1.5 rounded-full bg-tertiary"></span> Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-surface-variant text-on-surface-variant text-xs font-semibold">
                            <span class="w-1.5 h-1.5 rounded-full bg-outline"></span> Nonaktif
                        </span>
                    @endif
                </td>
                <td class="py-4 px-6 text-right">
                    <div class="flex items-center justify-end gap-1">
                        <a href="{{ route('admin.vendors.edit', $vendor->id) }}"
                           class="p-1.5 text-secondary hover:bg-secondary/10 rounded transition-colors"
                           title="Edit">
                            <span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1">edit</span>
                        </a>

                        {{-- Toggle Status Button --}}
                        <button type="button"
                                onclick="openToggleModal({{ $vendor->id }}, '{{ $vendor->nama }}', '{{ $vendor->status }}')"
                                class="p-1.5 rounded transition-colors {{ $vendor->status === 'aktif' ? 'text-slate-400 hover:bg-red-50 hover:text-error' : 'text-slate-400 hover:bg-tertiary-container/30 hover:text-tertiary' }}"
                                title="{{ $vendor->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}">
                            <span class="material-symbols-outlined text-sm">{{ $vendor->status === 'aktif' ? 'block' : 'check_circle' }}</span>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="py-16 text-center text-slate-400 font-body">
                    <span class="material-symbols-outlined text-5xl block mb-3">factory</span>
                    Tidak ada penyedia ditemukan.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between">
        <span class="text-sm text-slate-400 font-body">
            Menampilkan {{ $vendors->firstItem() ?? 0 }}–{{ $vendors->lastItem() ?? 0 }}
            dari {{ $vendors->total() }} vendor
        </span>
        {{ $vendors->links() }}
    </div>
</div>

{{-- Toggle Status Modal --}}
<div id="toggleModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 p-8">
        <div class="flex items-center gap-3 mb-4">
            <span id="modalIcon" class="material-symbols-outlined text-3xl text-error">block</span>
            <h3 class="text-lg font-bold font-headline text-on-surface" id="modalTitle">Nonaktifkan Vendor?</h3>
        </div>
        <p class="text-sm text-slate-500 font-body mb-6" id="modalDesc">
            Vendor ini akan dinonaktifkan dan tidak muncul di rekomendasi. Lanjutkan?
        </p>
        <div class="flex items-center justify-end gap-3">
            <button onclick="closeToggleModal()"
                    class="px-5 py-2 text-sm font-semibold text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors">
                Batal
            </button>
            <form id="toggleForm" method="POST">
                @csrf @method('PATCH')
                <button type="submit"
                        id="toggleBtn"
                        class="px-5 py-2 text-sm font-semibold bg-error text-white rounded-lg hover:opacity-90 transition-colors">
                    Nonaktifkan
                </button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openToggleModal(id, nama, status) {
    const isAktif = status === 'aktif';
    document.getElementById('toggleForm').action = `/admin/vendors/${id}/toggle`;
    document.getElementById('modalIcon').textContent = isAktif ? 'block' : 'check_circle';
    document.getElementById('modalIcon').className = `material-symbols-outlined text-3xl ${isAktif ? 'text-error' : 'text-tertiary'}`;
    document.getElementById('modalTitle').textContent = isAktif ? 'Nonaktifkan Vendor?' : 'Aktifkan Vendor?';
    document.getElementById('modalDesc').textContent = isAktif
        ? `"${nama}" akan dinonaktifkan dan tidak muncul di rekomendasi. Lanjutkan?`
        : `"${nama}" akan diaktifkan kembali dan muncul di rekomendasi. Lanjutkan?`;
    document.getElementById('toggleBtn').textContent = isAktif ? 'Nonaktifkan' : 'Aktifkan';
    document.getElementById('toggleBtn').className = `px-5 py-2 text-sm font-semibold rounded-lg hover:opacity-90 transition-colors ${isAktif ? 'bg-error text-white' : 'bg-tertiary text-white'}`;
    document.getElementById('toggleModal').classList.replace('hidden', 'flex');
}

function closeToggleModal() {
    document.getElementById('toggleModal').classList.replace('flex', 'hidden');
}

document.getElementById('toggleModal').addEventListener('click', function(e) {
    if (e.target === this) closeToggleModal();
});
</script>
@endpush
