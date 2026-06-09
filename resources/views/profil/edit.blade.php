@extends('layouts.profile')

@section('title', 'Kelola Profil')

@php
$avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($user->nama) . '&background=003366&color=fff&size=256';
$bannerUrl = 'https://images.unsplash.com/photo-1508514177221-188b1cf16e9d?auto=format&fit=crop&w=1600&q=80';
@endphp

@section('content')

<section class="relative h-[230px] overflow-hidden">
    <img src="{{ $bannerUrl }}" alt="" class="absolute inset-0 h-full w-full object-cover">

    <div class="absolute inset-0 bg-black/30"></div>

    <div class="absolute bottom-6 left-8 flex items-center gap-4">

<div class="absolute inset-0 bg-black/30"></div>

<div class="absolute bottom-6 left-8 flex items-center gap-4">

    <img
        src="{{ $avatarUrl }}"
        alt=""
        class="h-20 w-20 rounded-full border-4 border-white object-cover shadow-lg">

    <div class="text-white">

        <div class="flex items-center gap-2">

            <h1 class="text-3xl font-bold">
                {{ $user->nama }}
            </h1>

            <span class="rounded-full bg-white px-3 py-1 text-xs font-bold uppercase text-nt-navy">
                Donatur
            </span>

        </div>

        <p class="text-sm text-white/90">
            Bergabung sejak {{ $bergabung }}
        </p>

    </div>

</div>

</section>

<div class="mx-auto max-w-[1200px] px-4 py-8">

```
@if (session('success'))
    <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
        {{ session('success') }}
    </div>
@endif

<div class="grid gap-6 lg:grid-cols-[380px_1fr]">

    <!-- KOLOM KIRI -->
    <div class="space-y-6">

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <h2 class="mb-5 text-lg font-semibold text-nt-navy">
                Informasi Profil
            </h2>

            <form action="{{ route('profil.update') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Nama Lengkap
                    </label>

                    <input
                        type="text"
                        name="nama"
                        value="{{ old('nama', $user->nama) }}"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        value="{{ old('email', $user->email) }}"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        No. Telepon
                    </label>

                    <input
                        type="text"
                        name="no_telepon"
                        value="{{ old('no_telepon', $user->no_telepon) }}"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Lokasi
                    </label>

                    <input
                        type="text"
                        value="Jakarta Selatan, Indonesia"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                </div>

                <button
                    type="submit"
                    class="w-full rounded-xl bg-nt-accent py-3 font-semibold text-nt-navy">
                    Simpan Perubahan
                </button>

            </form>

        </div>

        <!-- IMPACT -->
        <div class="rounded-2xl bg-[#0D4A8B] p-6 text-white shadow">

            <p class="text-xs opacity-80">
                Total Dampak Anda
            </p>

            <h2 class="mt-3 text-4xl font-bold text-yellow-300">
                1,240
            </h2>

            <p class="text-sm">
                kWh Tereneralisasi
            </p>

            <p class="mt-2 text-xs text-white/70">
                Kontribusi Anda telah membantu menerangi 12 rumah.
            </p>

            <div class="mt-5 h-2 rounded-full bg-white/20">
                <div class="h-2 w-2/3 rounded-full bg-yellow-400"></div>
            </div>

        </div>

    </div>

    <!-- KOLOM KANAN -->
    <div class="space-y-6">

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <div class="mb-5 flex items-center justify-between">

                <h2 class="text-lg font-semibold text-nt-navy">
                    Riwayat Donasi
                </h2>

                <a href="#" class="text-sm text-blue-600">
                    Lihat Semua
                </a>

            </div>

            <div class="space-y-5">

                <div class="flex justify-between border-b pb-4">
                    <div>
                        <h3 class="font-medium">PLTS Desa Sukamaju</h3>
                        <p class="text-sm text-slate-500">1 Oktober 2023</p>
                    </div>

                    <div class="text-right">
                        <p class="font-semibold">Rp 2.500.000</p>
                        <span class="rounded bg-green-100 px-2 py-1 text-xs text-green-700">
                            SUKSES
                        </span>
                    </div>
                </div>

                <div class="flex justify-between border-b pb-4">
                    <div>
                        <h3 class="font-medium">Mikrohidro Sungai Barito</h3>
                        <p class="text-sm text-slate-500">28 September 2023</p>
                    </div>

                    <div class="text-right">
                        <p class="font-semibold">Rp 5.000.000</p>
                        <span class="rounded bg-green-100 px-2 py-1 text-xs text-green-700">
                            SUKSES
                        </span>
                    </div>
                </div>

                <div class="flex justify-between">
                    <div>
                        <h3 class="font-medium">Kincir Angin Pesisir Selatan</h3>
                        <p class="text-sm text-slate-500">10 September 2023</p>
                    </div>

                    <div class="text-right">
                        <p class="font-semibold">Rp 1.200.000</p>
                        <span class="rounded bg-yellow-100 px-2 py-1 text-xs text-yellow-700">
                            PENDING
                        </span>
                    </div>
                </div>

            </div>

        </div>

        <div class="grid gap-6 md:grid-cols-2">

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-semibold">🏆 Lencana</h3>
                <p class="mt-3 text-slate-600">Pahlawan Surya</p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-semibold">🌱 CO₂ Dikurangi</h3>
                <p class="mt-3 text-slate-600">450 kg</p>
            </div>

        </div>

    </div>

</div>

</div>

@endsection
