@extends('layouts.admin')

@section('title', 'Detail Pengguna')
@section('page_heading', 'Detail Pengguna')
@section('breadcrumbs', $user->nama)

@section('content')

<div class="space-y-6">

    <a
        href="{{ route('admin.users.index') }}"
        class="inline-flex rounded-lg border px-4 py-2 text-sm">

        ← Kembali

    </a>

    <div class="grid gap-6 lg:grid-cols-3">

        <div class="lg:col-span-2 rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">

            <div class="flex items-center gap-5">

                <img
                    src="https://ui-avatars.com/api/?name={{ urlencode($user->nama) }}"
                    class="h-24 w-24 rounded-full">

                <div>

                    <h2 class="text-2xl font-bold">
                        {{ $user->nama }}
                    </h2>

                    <p class="text-slate-500">
                        {{ $user->email }}
                    </p>

                    <div class="mt-2">

                        <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-bold text-green-700">

                            {{ strtoupper($user->role) }}

                        </span>

                    </div>

                </div>

            </div>

        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">

            <h3 class="font-bold">
                Aksi Cepat
            </h3>

            <form
                action="{{ route('admin.users.role',$user->id_donatur) }}"
                method="POST"
                class="mt-4">

                @csrf
                @method('PUT')

                <select
                    name="role"
                    class="w-full rounded-lg border px-3 py-2">

                    <option value="admin">Admin</option>
                    <option value="penyedia">Penyedia</option>
                    <option value="donatur">Donatur</option>

                </select>

                <button
                    class="mt-3 w-full rounded-lg bg-blue-600 py-2 text-white">

                    Ubah Role

                </button>

            </form>

            <form
                action="{{ route('admin.users.status',$user->id_donatur) }}"
                method="POST"
                class="mt-3">

                @csrf
                @method('PUT')

                <button
                    class="w-full rounded-lg bg-red-600 py-2 text-white">

                    Nonaktifkan Akun

                </button>

            </form>

        </div>

    </div>

    <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">

        <h3 class="mb-4 font-bold">
            Informasi Akun
        </h3>

        <div class="grid gap-4 md:grid-cols-2">

            <div>
                <p class="text-sm text-slate-500">
                    Nama Lengkap
                </p>

                <p>
                    {{ $user->nama }}
                </p>
            </div>

            <div>
                <p class="text-sm text-slate-500">
                    Email
                </p>

                <p>
                    {{ $user->email }}
                </p>
            </div>

            <div>
                <p class="text-sm text-slate-500">
                    No Telepon
                </p>

                <p>
                    {{ $user->no_telepon ?? '-' }}
                </p>
            </div>

            <div>
                <p class="text-sm text-slate-500">
                    Role
                </p>

                <p>
                    {{ strtoupper($user->role) }}
                </p>
            </div>

        </div>

    </div>

</div>

@endsection