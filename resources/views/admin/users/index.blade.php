@extends('layouts.admin')

@section('title', 'Manajemen Pengguna')
@section('page_heading', 'Manajemen Pengguna')
@section('breadcrumbs', 'Kelola akun pengguna platform')

@section('content')

<div class="space-y-6">

    {{-- FILTER --}}
    <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
        <form method="GET" class="grid gap-3 md:grid-cols-4">

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari nama atau email..."
                class="rounded-lg border border-slate-300 px-4 py-2">

            <select
                name="role"
                class="rounded-lg border border-slate-300 px-4 py-2">

                <option value="">Semua Role</option>
                <option value="admin">Admin</option>
                <option value="penyedia">Penyedia</option>
                <option value="donatur">Donatur</option>

            </select>

            <select
                name="status"
                class="rounded-lg border border-slate-300 px-4 py-2">

                <option value="">Semua Status</option>
                <option value="aktif">Aktif</option>
                <option value="nonaktif">Nonaktif</option>

            </select>

            <button
                class="rounded-lg bg-yellow-400 font-semibold text-slate-900">

                Cari

            </button>

        </form>
    </div>

    {{-- STAT --}}
    <div class="grid gap-4 md:grid-cols-3">

        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <p class="text-sm text-slate-500">Total Pengguna</p>
            <h2 class="mt-2 text-3xl font-bold">
                {{ $totalUsers }}
            </h2>
        </div>

        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <p class="text-sm text-slate-500">Aktif</p>
            <h2 class="mt-2 text-3xl font-bold text-green-600">
                {{ $activeUsers }}
            </h2>
        </div>

        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <p class="text-sm text-slate-500">Nonaktif</p>
            <h2 class="mt-2 text-3xl font-bold text-red-600">
                {{ $inactiveUsers }}
            </h2>
        </div>

    </div>

    {{-- TABLE --}}
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-slate-200 overflow-hidden">

        <table class="w-full text-sm">

            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left">Nama</th>
                    <th class="px-4 py-3 text-left">Email</th>
                    <th class="px-4 py-3 text-left">Role</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Aksi</th>
                </tr>
            </thead>

            <tbody>

                @forelse($users as $user)

                <tr class="border-t">

                    <td class="px-4 py-4">
                        {{ $user->nama }}
                    </td>

                    <td class="px-4 py-4">
                        {{ $user->email }}
                    </td>

                    <td class="px-4 py-4">

                        <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-700">

                            {{ strtoupper($user->role) }}

                        </span>

                    </td>

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

                    <td class="px-4 py-4">

                        <a
                            href="{{ route('admin.users.show',$user->id_donatur) }}"
                            class="rounded-lg border px-3 py-2 text-xs font-semibold">

                            Lihat Detail

                        </a>

                    </td>

                </tr>

                @empty

                <tr>

                    <td colspan="5" class="px-6 py-10 text-center text-slate-500">

                        Tidak ada pengguna

                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    {{ $users->links() }}

</div>

@endsection