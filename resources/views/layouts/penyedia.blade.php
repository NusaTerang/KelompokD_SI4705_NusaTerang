<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'NusaTerang') — Vendor Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen bg-surface font-body text-on-surface antialiased">

@php
    $proyekOpen = request()->routeIs('vendor.proyek.*');
@endphp

<div class="flex min-h-screen">
    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 z-40 hidden w-64 flex-col bg-deep-navy text-white lg:flex shadow-2xl">
        <div class="px-6 py-6 mb-2">
            <h1 class="text-xl font-black text-white font-headline tracking-tight">NusaTerang</h1>
            <p class="text-[10px] uppercase tracking-widest text-white/50 font-bold">Renewable Energy</p>
        </div>

        <nav class="flex-1 space-y-1 px-3 overflow-y-auto">
            <a href="{{ route('vendor.dashboard') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-headline tracking-wide transition-all duration-200
                      {{ request()->routeIs('vendor.dashboard') ? 'bg-white/10 text-white font-bold' : 'text-white/70 hover:text-white hover:bg-white/5' }}">
                <span class="material-symbols-outlined {{ request()->routeIs('vendor.dashboard') ? 'active-fill' : '' }}">dashboard</span>
                Dashboard
            </a>

            <a href="{{ route('vendor.proyek.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-headline tracking-wide transition-all duration-200
                      {{ $proyekOpen ? 'bg-white/10 text-white font-bold' : 'text-white/70 hover:text-white hover:bg-white/5' }}">
                <span class="material-symbols-outlined {{ $proyekOpen ? 'active-fill' : '' }}">folder_special</span>
                Proyek Saya
            </a>

            <a href="#"
               class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-headline tracking-wide text-white/70 hover:text-white hover:bg-white/5 transition-all duration-200">
                <span class="material-symbols-outlined">domain</span>
                Profil Perusahaan
            </a>

            <a href="#"
               class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-headline tracking-wide text-white/70 hover:text-white hover:bg-white/5 transition-all duration-200">
                <span class="material-symbols-outlined">notifications</span>
                Notifikasi
            </a>
        </nav>

        <div class="mt-auto border-t border-white/10 px-3 py-4 space-y-1">
            <a href="#"
               class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-headline tracking-wide text-white/70 hover:text-white hover:bg-white/5 transition-all duration-200">
                <span class="material-symbols-outlined">settings</span>
                Settings
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="flex w-full items-center gap-3 px-4 py-3 rounded-lg text-sm font-headline tracking-wide text-white/70 hover:text-white hover:bg-white/5 transition-all duration-200">
                    <span class="material-symbols-outlined">logout</span>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <div class="flex min-h-screen flex-1 flex-col lg:pl-64">
        {{-- Topbar --}}
        <header class="sticky top-0 z-30 flex justify-between items-center px-6 lg:px-8 h-16 bg-white/80 backdrop-blur-md shadow-sm">
            <nav class="flex items-center gap-1.5 text-sm text-on-surface-variant font-medium">
                @yield('breadcrumbs')
            </nav>
            <div class="flex items-center gap-4">
                <button class="w-9 h-9 flex items-center justify-center rounded-full text-slate-500 hover:bg-slate-50 transition-colors">
                    <span class="material-symbols-outlined text-[20px]">help</span>
                </button>
                <button class="w-9 h-9 flex items-center justify-center rounded-full text-slate-500 hover:bg-slate-50 transition-colors">
                    <span class="material-symbols-outlined text-[20px]">settings</span>
                </button>
                @auth
                    <div class="w-9 h-9 rounded-full bg-solar-gold flex items-center justify-center border-2 border-solar-gold shadow-sm">
                        <span class="text-deep-navy font-black text-xs">
                            {{ strtoupper(substr(auth()->user()->nama ?? 'V', 0, 2)) }}
                        </span>
                    </div>
                @endauth
            </div>
        </header>

        <main class="flex-1 pt-6 pb-12 px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 bg-sustainability-green/10 border border-sustainability-green/30 text-sustainability-green rounded-xl px-5 py-4 text-sm font-semibold">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('info'))
                <div class="mb-6 flex items-center gap-3 bg-deep-navy/10 border border-deep-navy/20 text-deep-navy rounded-xl px-5 py-4 text-sm font-semibold">
                    <span class="material-symbols-outlined text-[18px]">info</span>
                    {{ session('info') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<style>
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        vertical-align: middle;
    }
    .active-fill {
        font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
    h1, h2, h3, h4 { font-family: 'Poppins', sans-serif; }
</style>

@stack('scripts')
</body>
</html>
