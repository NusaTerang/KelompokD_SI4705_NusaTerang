@extends('layouts.profile')

@section('title', 'Kelola Profil')

@php
    $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($user->nama) . '&background=003366&color=fff&size=256';
    $bannerUrl = 'https://images.unsplash.com/photo-1508514177221-188b1cf16e9d?auto=format&fit=crop&w=1600&q=80';
@endphp

@section('content')
    <section class="relative h-52 overflow-hidden sm:h-60 md:h-64">
        <img src="{{ $bannerUrl }}" alt="" class="absolute inset-0 h-full w-full object-cover" />
        <div class="absolute inset-0 bg-gradient-to-t from-nt-navy/90 via-nt-navy/40 to-transparent"></div>
        <div class="relative mx-auto flex h-full max-w-3xl items-end gap-4 px-4 pb-8 sm:px-6 md:items-center md:pb-10">
            <div class="relative shrink-0">
                <img src="{{ $avatarUrl }}" alt="" class="h-24 w-24 rounded-full border-4 border-white object-cover shadow-lg md:h-28 md:w-28" />
            </div>
            <div class="min-w-0 flex-1 text-white">
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-2xl font-bold tracking-tight md:text-3xl">{{ $user->nama }}</h1>
                    <span class="rounded-full bg-white px-3 py-0.5 text-xs font-bold uppercase tracking-wide text-nt-navy">DONATUR</span>
                </div>
                <p class="mt-1 text-sm text-white/85">Bergabung sejak {{ $bergabung }}</p>
            </div>
        </div>
    </section>

    <div class="mx-auto max-w-3xl px-4 pb-12 sm:px-6">
        @if (session('success'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900" role="status">{{ session('success') }}</div>
        @endif

        <div class="mt-8">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="mb-5 flex items-center gap-2 text-lg font-semibold text-nt-navy">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-sky-100 text-sky-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                    </span>
                    Informasi Profil
                </h2>
                <form action="{{ route('profil.update') }}" method="post" class="space-y-4">
                    @csrf
                    @method('put')
                    <div>
                        <label for="nama" class="mb-1.5 block text-sm font-medium text-slate-700">Nama Lengkap</label>
                        <input type="text" name="nama" id="nama" value="{{ old('nama', $user->nama) }}" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-nt-navy focus:bg-white focus:outline-none focus:ring-2 focus:ring-nt-navy/20" />
                        @error('nama')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-medium text-slate-700">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-nt-navy focus:bg-white focus:outline-none focus:ring-2 focus:ring-nt-navy/20" />
                        @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="no_telepon" class="mb-1.5 block text-sm font-medium text-slate-700">No. Telepon</label>
                        <input type="text" name="no_telepon" id="no_telepon" value="{{ old('no_telepon', $user->no_telepon) }}" placeholder="+62 …" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-nt-navy focus:bg-white focus:outline-none focus:ring-2 focus:ring-nt-navy/20" />
                        @error('no_telepon')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="mt-2 flex w-full items-center justify-center gap-2 rounded-xl bg-nt-accent py-3 text-sm font-bold text-nt-navy shadow-sm hover:bg-nt-accent-hover">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                        Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
