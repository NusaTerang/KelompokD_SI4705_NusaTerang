@extends('layouts.admin')

@section('title', 'Manajemen Pengguna')
@section('page_heading', 'Manajemen Pengguna')
@section('breadcrumbs', 'Kelola akun pengguna platform')

@section('content')

<div class="space-y-6">

    {{-- FILTER --}}
    <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
        <form method="GET" class="grid grid-cols-1 lg:grid-cols-12 gap-4">

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari nama atau email pengguna..."
                class="lg:col-span-5 w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-2 focus:ring-yellow-400">

            <select
                name="role"
                class="lg:col-span-2 w-full rounded-xl border border-slate-200 px-4 py-3">

                <option value="">Semua Role</option>
                <option value="admin">Admin</option>
                <option value="penyedia">Penyedia</option>
                <option value="donatur">Donatur</option>

            </select>

            <select
                name="status"
                class="lg:col-span-2 w-full rounded-xl border border-slate-200 px-4 py-3">

                <option value="">Semua Status</option>
                <option value="aktif">Aktif</option>
                <option value="nonaktif">Nonaktif</option>

            </select>

            <button
                class="lg:col-span-3 w-full rounded-xl bg-yellow-400 py-3 font-semibold hover:bg-yellow-500 transition">
                Cari
            </button>

        </form>
    </div>

    {{-- STAT --}}
{{-- STAT --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

    <!-- Total Pengguna -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex items-center gap-5 hover:shadow-md transition">
        <div class="flex items-center gap-4">

            <div class="w-16 h-16 rounded-xl bg-blue-100 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="w-8 h-8 text-blue-600"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">

                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M17 20h5V18a4 4 0 00-4-4h-1m-4 6H4v-2a4 4 0 014-4h5m0-4a4 4 0 100-8 4 4 0 000 8z"/>

                </svg>
            </div>

            <div>
                <h2 class="text-4xl font-bold text-blue-600">
                    {{ $totalUsers }}
                </h2>

                <p class="mt-1 text-slate-500">
                    Total Pengguna
                </p>
            </div>

        </div>
    </div>

    <!-- Aktif -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex items-center gap-5 hover:shadow-md transition">
        <div class="flex items-center gap-4">

            <div class="w-16 h-16 rounded-xl bg-green-100 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="w-8 h-8 text-green-600"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">

                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 12l2 2 4-4"/>

                </svg>
            </div>

            <div>
                <h2 class="text-4xl font-bold text-green-600">
                    {{ $activeUsers }}
                </h2>

                <p class="mt-1 text-slate-500">
                    Aktif
                </p>
            </div>

        </div>
    </div>

    <!-- Nonaktif -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex items-center gap-5 hover:shadow-md transition">
        <div class="flex items-center gap-4">

            <div class="w-16 h-16 rounded-xl bg-red-100 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="w-8 h-8 text-red-600"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">

                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"/>

                </svg>
            </div>

            <div>
                <h2 class="text-4xl font-bold text-red-600">
                    {{ $inactiveUsers }}
                </h2>

                <p class="mt-1 text-slate-500">
                    Nonaktif
                </p>
            </div>

        </div>
    </div>

</div>

    {{-- TABLE --}}
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-slate-200 overflow-hidden">

        <table class="min-w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left">Nama Pengguna</th>
                    <th class="px-4 py-3 text-left">Email</th>
                    <th class="px-4 py-3 text-left">Role</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Tanggal Registrasi</th>
                    <th class="px-4 py-3 text-left">Aksi</th>
                </tr>
            </thead>

            <tbody>

                @forelse($users as $user)

                <tr class="border-t hover:bg-slate-50 transition">

                    <td class="px-4 py-4">

                        <div class="flex items-center gap-3">

                            <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center font-bold text-slate-700">

                                {{ strtoupper(substr($user->nama,0,1)) }}

                            </div>

                            <div>

                                <div>
                                    <div class="font-semibold text-slate-800">
                                        {{ $user->nama }}
                                    </div>

                                    <div class="text-xs text-slate-500">
                                        {{ $user->email }}
                                    </div>
                                </div>

                            </div>

                        </div>

                    </td>

                    <td class="px-4 py-4">
                        {{ $user->email }}
                    </td>

                    <td class="px-4 py-4">

                        @php
                        $roleColor = match(strtolower($user->role)){
                            'admin' => 'bg-purple-100 text-purple-700',
                            'penyedia' => 'bg-blue-100 text-blue-700',
                            'donatur' => 'bg-green-100 text-green-700',
                            default => 'bg-gray-100 text-gray-700'
                        };
                        @endphp

                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $roleColor }}">
                            {{ strtoupper($user->role) }}
                        </span>
                    </td>

                    {{-- STATUS --}}
                    <td class="px-4 py-4">

                        @if(($user->status ?? 'aktif') == 'aktif')
                            <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-bold text-green-700">
                                AKTIF
                            </span>
                        @else
                            <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-700">
                                NONAKTIF
                            </span>
                        @endif

                    </td>

                    {{-- TANGGAL REGISTRASI --}}
                    <td class="px-4 py-4 text-slate-500">
                        {{ optional($user->created_at)->format('d M Y') }}
                    </td>

                    {{-- AKSI --}}
                    <td class="px-4 py-4">

                        <a
                            href="{{ route('admin.users.show', $user->id_donatur) }}"
                            class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-xs font-semibold text-blue-700 hover:bg-blue-50 transition">

                            Lihat Detail

                        </a>

                    </td>
                </tr>

                @empty

                <tr>

                    <td colspan="6" class="px-6 py-10 text-center text-slate-500">

                        Tidak ada pengguna

                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <div class="flex justify-between items-center mt-5">

    <span class="text-sm text-slate-500">

        Menampilkan

        {{ $users->firstItem() }}

        -

        {{ $users->lastItem() }}

        dari

        {{ $users->total() }}

        pengguna

    </span>

    {{ $users->links() }}

</div>

</div>

@endsection