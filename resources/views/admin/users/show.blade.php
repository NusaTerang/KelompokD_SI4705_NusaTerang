@extends('layouts.admin')

@section('title', 'Detail Pengguna')
@section('page_heading', 'Detail Pengguna')
@section('breadcrumbs', $user->nama)

@section('content')

<div class="space-y-6">

    {{-- Tombol Kembali --}}
    <a href="{{ route('admin.users.index') }}"
        class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm hover:bg-slate-50">

        ← Kembali

    </a>

    {{-- Profil + Aksi Cepat --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Profil --}}
        <div class="lg:col-span-2 rounded-2xl bg-white p-8 shadow-sm ring-1 ring-slate-200">

            <div class="flex items-center gap-6">

                <img
                    src="https://ui-avatars.com/api/?name={{ urlencode($user->nama) }}&background=E0F2FE&color=1E3A8A&size=128"
                    class="h-28 w-28 rounded-full shadow">

                <div>

                    <div class="flex items-center gap-3">

                        <h2 class="text-3xl font-bold text-slate-800">

                            {{ $user->nama }}

                        </h2>

                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">

                            {{ strtoupper($user->status ?? 'AKTIF') }}

                        </span>

                    </div>

                    <div class="mt-3">

                        <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">

                            {{ strtoupper($user->role) }}

                        </span>

                    </div>

                    <p class="mt-4 text-sm text-slate-500">

                        Bergabung sejak
                        {{ optional($user->created_at)->format('d F Y') }}

                    </p>

                    <p class="text-sm text-slate-500">

                        Terakhir login :
                        -

                    </p>

                </div>

            </div>

        </div>

        {{-- Aksi Cepat --}}
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">

            <h3 class="mb-5 text-lg font-bold text-slate-800">

                Aksi Cepat

            </h3>

            <button
                type="button"
                onclick="openRoleModal()"
                class="w-full rounded-xl border border-blue-500 py-3 font-semibold text-blue-600 hover:bg-blue-50 transition">

                👤 Ubah Role

            </button>

            <button
                type="button"
                onclick="openStatusModal()"
                class="mt-4 w-full rounded-xl border border-red-500 py-3 font-semibold text-red-600 hover:bg-red-50 transition">

                ⛔ Nonaktifkan Akun

            </button>

        </div>

    </div>

        {{-- Informasi Akun & Role --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Informasi Akun --}}
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">

            <h3 class="mb-6 text-xl font-bold text-slate-800">
                Informasi Akun
            </h3>

            <div class="space-y-5">

                <div class="flex justify-between border-b pb-3">
                    <span class="text-slate-500">Nama Lengkap</span>
                    <span class="font-medium text-slate-800">
                        {{ $user->nama }}
                    </span>
                </div>

                <div class="flex justify-between border-b pb-3">
                    <span class="text-slate-500">Email</span>
                    <span class="font-medium text-slate-800">
                        {{ $user->email }}
                    </span>
                </div>

                <div class="flex justify-between border-b pb-3">
                    <span class="text-slate-500">No. Telepon</span>
                    <span class="font-medium text-slate-800">
                        {{ $user->no_telepon ?? '-' }}
                    </span>
                </div>

                <div class="flex justify-between border-b pb-3">
                    <span class="text-slate-500">Tanggal Registrasi</span>
                    <span class="font-medium text-slate-800">
                        {{ optional($user->created_at)->format('d F Y') }}
                    </span>
                </div>

                <div class="flex justify-between">

                    <span class="text-slate-500">
                        Status Akun
                    </span>

                    @if(($user->status ?? 'aktif') == 'aktif')

                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-sm font-semibold text-emerald-700">

                            AKTIF

                        </span>

                    @else

                        <span class="rounded-full bg-red-100 px-3 py-1 text-sm font-semibold text-red-700">

                            NONAKTIF

                        </span>

                    @endif

                </div>

            </div>

        </div>

        {{-- Informasi Role --}}
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">

            <h3 class="mb-6 text-xl font-bold text-slate-800">

                Informasi Role

            </h3>

            <div>

                <p class="text-slate-500 mb-2">

                    Role Saat Ini

                </p>

                <span class="rounded-full bg-blue-100 px-4 py-2 text-sm font-semibold text-blue-700">

                    {{ strtoupper($user->role) }}

                </span>

            </div>

            <div class="mt-8">

                <h4 class="mb-3 font-semibold text-slate-700">

                    Hak Akses

                </h4>

                @if($user->role == 'admin')

                    <ul class="space-y-3 text-slate-600">

                        <li>✅ Mengelola seluruh pengguna</li>

                        <li>✅ Mengelola proyek energi</li>

                        <li>✅ Mengubah role pengguna</li>

                        <li>✅ Mengakses dashboard admin</li>

                    </ul>

                @elseif($user->role == 'penyedia')

                    <ul class="space-y-3 text-slate-600">

                        <li>✅ Membuat proyek energi</li>

                        <li>✅ Mengelola proyek sendiri</li>

                        <li>✅ Memperbarui progres proyek</li>

                        <li>✅ Melihat donasi proyek</li>

                    </ul>

                @else

                    <ul class="space-y-3 text-slate-600">

                        <li>✅ Melakukan donasi</li>

                        <li>✅ Melihat progress proyek</li>

                        <li>✅ Riwayat donasi</li>

                        <li>✅ Mengelola profil pribadi</li>

                    </ul>

                @endif

            </div>

        </div>

    </div>

        {{-- Riwayat Aktivitas --}}
    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">

        <div class="flex items-center justify-between mb-6">

            <h3 class="text-xl font-bold text-slate-800">

                Riwayat Aktivitas

            </h3>

            <button
                class="rounded-lg border border-slate-300 px-4 py-2 text-sm hover:bg-slate-50">

                Lihat Semua

            </button>

        </div>

        <div class="space-y-6">

            {{-- Login --}}
            <div class="flex gap-4">

                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100">

                    🔐

                </div>

                <div>

                    <p class="font-semibold text-slate-800">

                        Login ke Sistem

                    </p>

                    <p class="text-sm text-slate-500">

                        {{ optional($user->updated_at)->format('d F Y H:i') ?? '-' }}

                    </p>

                </div>

            </div>

            {{-- Profil --}}
            <div class="flex gap-4">

                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100">

                    👤

                </div>

                <div>

                    <p class="font-semibold text-slate-800">

                        Update Profil

                    </p>

                    <p class="text-sm text-slate-500">

                        Belum tersedia

                    </p>

                </div>

            </div>

            {{-- Role --}}
            <div class="flex gap-4">

                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-yellow-100">

                    🔄

                </div>

                <div>

                    <p class="font-semibold text-slate-800">

                        Perubahan Role

                    </p>

                    <p class="text-sm text-slate-500">

                        Belum tersedia

                    </p>

                </div>

            </div>

            {{-- Registrasi --}}
            <div class="flex gap-4">

                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-purple-100">

                    🎉

                </div>

                <div>

                    <p class="font-semibold text-slate-800">

                        Registrasi Akun

                    </p>

                    <p class="text-sm text-slate-500">

                        {{ optional($user->created_at)->format('d F Y') }}

                    </p>

                </div>

            </div>

        </div>

    </div>

    {{-- Modal Ubah Role --}}
<div
    id="roleModal"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">

    <div class="w-full max-w-2xl rounded-2xl bg-white shadow-2xl">

        {{-- Header --}}
        <div class="flex items-center justify-between rounded-t-2xl bg-[#0B3B75] px-6 py-4">
            <form action="{{ route('admin.users.role', $user) }}" method="POST">

                @csrf
                @method('PUT')
                <h2 class="text-xl font-bold text-white">
                    Ubah Role Pengguna
                </h2>

                <button
                    type="button"
                    onclick="closeRoleModal()"
                    class="text-2xl text-white hover:text-gray-300">

                    ×

                </button>

            </div>

                <div class="space-y-6 p-6">

                    {{-- Nama --}}
                    <div>

                        <label class="mb-2 block font-medium text-slate-700">

                            Nama Pengguna

                        </label>

                        <input
                            readonly
                            value="{{ $user->nama }}"
                            class="w-full rounded-lg border bg-slate-100 px-4 py-3">

                    </div>

                    {{-- Email --}}
                    <div>

                        <label class="mb-2 block font-medium text-slate-700">

                            Email

                        </label>

                        <input
                            readonly
                            value="{{ $user->email }}"
                            class="w-full rounded-lg border bg-slate-100 px-4 py-3">

                    </div>

                    {{-- Role sekarang --}}
                    <div>

                        <label class="mb-2 block font-medium text-slate-700">

                            Role Saat Ini

                        </label>

                        <span class="inline-flex rounded-full bg-green-100 px-4 py-2 font-semibold text-green-700">

                            {{ strtoupper($user->role) }}

                        </span>

                    </div>

                    {{-- Pilihan --}}
                    <div>

                        <h3 class="mb-4 font-semibold text-slate-800">

                            Pilih Role Baru

                        </h3>

                        <div class="space-y-4">

                            <label class="flex cursor-pointer items-start gap-3 rounded-xl border p-4 hover:bg-slate-50">

                                <input type="radio"
                                    name="role"
                                    value="admin"
                                    {{ $user->role=='admin' ? 'checked' : '' }}>

                                <div>

                                    <p class="font-semibold">
                                        Admin
                                    </p>

                                    <p class="text-sm text-slate-500">
                                        Memiliki akses penuh ke seluruh sistem.
                                    </p>

                                </div>

                            </label>

                            <label class="flex cursor-pointer items-start gap-3 rounded-xl border p-4 hover:bg-slate-50">

                                <input type="radio"
                                    name="role"
                                    value="penyedia"
                                    {{ $user->role=='penyedia' ? 'checked' : '' }}>

                                <div>

                                    <p class="font-semibold">
                                        Penyedia
                                    </p>

                                    <p class="text-sm text-slate-500">
                                        Mengelola proyek energi dan progres proyek.
                                    </p>

                                </div>

                            </label>

                            <label class="flex cursor-pointer items-start gap-3 rounded-xl border p-4 hover:bg-slate-50">

                                <input type="radio"
                                    name="role"
                                    value="donatur"
                                    {{ $user->role=='donatur' ? 'checked' : '' }}>

                                <div>

                                    <p class="font-semibold">
                                        Donatur
                                    </p>

                                    <p class="text-sm text-slate-500">
                                        Melakukan donasi dan memantau perkembangan proyek.
                                    </p>

                                </div>

                            </label>

                        </div>

                    </div>

                </div>

                {{-- Footer --}}
                <div class="flex justify-end gap-3 border-t px-6 py-4">

                    <button
                        type="button"
                        onclick="closeRoleModal()"
                        class="rounded-xl border border-slate-300 px-6 py-2 hover:bg-slate-100">

                        Batal

                    </button>

                    <button
                        type="submit"
                        class="rounded-xl bg-yellow-400 px-6 py-2 font-semibold hover:bg-yellow-500">

                        Simpan Perubahan

                    </button>
            </form>

            </div>

    </div>

</div>
{{-- Modal Nonaktifkan --}}
<div
    id="statusModal"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">

    <div class="w-full max-w-md overflow-visible rounded-2xl bg-white shadow-2xl">

        <div class="flex flex-col p-8 text-center">

            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-100 text-3xl">

                ⚠️

            </div>

            <h2 class="mt-5 text-2xl font-bold">

                Nonaktifkan Akun?

            </h2>

            <p class="mt-3 text-slate-500">

                Pengguna <strong>{{ $user->nama }}</strong> tidak dapat mengakses sistem sampai diaktifkan kembali.

            </p>

            <form
                action="{{ route('admin.users.status', $user) }}"
                method="POST"
                class="mt-8">

            @csrf
            @method('PUT')

            <div class="flex justify-center gap-4">

                <button
                    type="button"
                    onclick="closeStatusModal()"
                    class="rounded-xl border border-slate-300 px-6 py-2">
                    Batal
                </button>

                <button
                    type="submit"
                    class="rounded-xl bg-red-600 px-6 py-2 text-white">
                    Ya, Nonaktifkan
                </button>

            </div>

        </form>

    </div>

</div>

@push('scripts')
<script>

function openRoleModal(){
    const modal = document.getElementById('roleModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeRoleModal(){
    const modal = document.getElementById('roleModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function openStatusModal(){
    const modal = document.getElementById('statusModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeStatusModal(){
    const modal = document.getElementById('statusModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

document.addEventListener('keydown', function(e){

    if(e.key === "Escape"){

        closeRoleModal();
        closeStatusModal();

    }

});

</script>
@endpush
@endsection