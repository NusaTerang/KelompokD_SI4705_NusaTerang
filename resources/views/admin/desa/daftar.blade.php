@extends('layouts.admin')

@section('title', 'Kelola Data Desa')

@section('breadcrumbs')
    <span>Dashboard</span>
    <span class="mx-1 text-slate-400">/</span>
    <span>Data Desa</span>
    <span class="mx-1 text-slate-400">/</span>
    <span class="font-medium text-slate-700">Kelola Data</span>
@endsection

@section('page_heading', 'Kelola Data Desa')

@section('content')
    <div class="mx-auto max-w-7xl space-y-4">
        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900" role="status">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-slate-600">Kelola seluruh data desa yang tersimpan di sistem.</p>
            <a href="{{ route('desa.input') }}" class="inline-flex items-center justify-center rounded-lg bg-nt-accent px-4 py-2.5 text-sm font-bold text-nt-navy shadow-sm hover:bg-nt-accent-hover">
                + Input Data Baru
            </a>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Nama Desa</th>
                            <th class="px-4 py-3">Provinsi</th>
                            <th class="px-4 py-3">Kabupaten</th>
                            <th class="px-4 py-3">Koordinat</th>
                            <th class="px-4 py-3">Sumber</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white text-slate-700">
                        @forelse (($desas ?? collect()) as $d)
                            <tr class="hover:bg-slate-50/80">
                                <td class="whitespace-nowrap px-4 py-3 font-mono text-xs">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 font-medium text-nt-navy">{{ $d->nama_desa }}</td>
                                <td class="px-4 py-3">{{ $d->provinsi }}</td>
                                <td class="px-4 py-3">{{ $d->kabupaten }}</td>
                                <td class="max-w-[140px] truncate px-4 py-3 text-xs text-slate-500" title="{{ $d->koordinat ?? '—' }}">{{ $d->koordinat ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full bg-sky-100 px-2 py-0.5 text-xs font-medium text-sky-800">{{ $d->sumber }}</span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    <a href="{{ route('desa.edit', $d->id_desa) }}"
                                    class="font-medium text-nt-navy hover:underline">
                                    Edit
                                    </a>

                                    <span class="mx-2 text-slate-300">|</span>

                                    <form action="{{ route('desa.destroy', $d->id_desa) }}"
                                        method="POST"
                                        class="inline"
                                        onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="font-medium text-red-600 hover:underline">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-10 text-center text-sm text-slate-500">
                                    Tidak ada data. Tambah dari halaman Input Data Desa.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
