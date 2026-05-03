<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'NusaTerang') — {{ config('app.name', 'NusaTerang') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen bg-nt-surface font-sans text-slate-800 antialiased">
    @php
        $dataDesaOpen = request()->routeIs('desa.*');
    @endphp

    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside class="fixed inset-y-0 left-0 z-40 hidden w-64 flex-col bg-nt-navy text-white lg:flex">
            <div class="border-b border-white/10 px-5 py-6">
                <p class="text-lg font-bold leading-tight tracking-tight">NusaTerang</p>
                <p class="text-[10px] font-semibold uppercase tracking-widest text-white/60">Renewable Energy</p>
            </div>

            <nav class="flex flex-1 flex-col gap-0.5 overflow-y-auto px-3 py-4">
                <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-white/80 hover:bg-white/10 hover:text-white">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-md bg-white/10">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
                    </span>
                    Dashboard
                </a>
                <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-white/80 hover:bg-white/10 hover:text-white">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-md bg-white/10">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" /></svg>
                    </span>
                    Proyek Energi
                </a>

                <div class="mt-0.5">
                    <div class="relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium {{ $dataDesaOpen ? 'bg-nt-navy-light text-white' : 'text-white/80' }}">
                        @if ($dataDesaOpen)
                            <span class="absolute left-0 top-1/2 h-9 w-1 -translate-y-1/2 rounded-r bg-nt-accent" aria-hidden="true"></span>
                        @endif
                        <span class="relative ml-1 flex h-8 w-8 shrink-0 items-center justify-center rounded-md bg-white/10">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12M9 3v2.25m0 4.5V21" /></svg>
                        </span>
                        <span class="relative flex-1">Data Desa</span>
                    </div>
                    <div class="ml-4 mt-1 space-y-0.5 border-l border-white/15 pl-3">
                        <a href="{{ route('desa.input') }}" class="block rounded-md py-1.5 pl-2 text-xs font-medium {{ request()->routeIs('desa.input') ? 'bg-white/10 text-nt-accent' : 'text-white/70 hover:text-white' }}">Input Data</a>
                        <a href="{{ route('desa.kelola') }}" class="block rounded-md py-1.5 pl-2 text-xs font-medium {{ request()->routeIs('desa.kelola') ? 'bg-white/10 text-nt-accent' : 'text-white/70 hover:text-white' }}">Desa Prioritas</a>
                        <a href="{{ route('desa.daftar') }}" class="block rounded-md py-1.5 pl-2 text-xs font-medium {{ request()->routeIs('desa.daftar') ? 'bg-white/10 text-nt-accent' : 'text-white/70 hover:text-white' }}">Kelola Data (Tabel)</a>
                    </div>
                </div>

                <a href="#" class="mt-0.5 flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-white/80 hover:bg-white/10 hover:text-white">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-md bg-white/10">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h5.25M9 3v18M9.75 3h5.25m0 0H21m-5.25 0V9m0 0H9.75m5.25 0V21" /></svg>
                    </span>
                    Penyedia Energi
                </a>
                <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-white/80 hover:bg-white/10 hover:text-white">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-md bg-white/10">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" /></svg>
                    </span>
                    Donasi
                </a>
                <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-white/80 hover:bg-white/10 hover:text-white">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-md bg-white/10">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
                    </span>
                    Notifikasi
                </a>
            </nav>

            <div class="mt-auto border-t border-white/10 px-3 py-4">
                <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-white/80 hover:bg-white/10 hover:text-white">
                    <span class="flex h-8 w-8 items-center justify-center rounded-md bg-white/10">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    </span>
                    Settings
                </a>
                <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-white/80 hover:bg-white/10 hover:text-white">
                    <span class="flex h-8 w-8 items-center justify-center rounded-md bg-white/10">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" /></svg>
                    </span>
                    Logout
                </a>
            </div>
        </aside>

        <div class="flex min-h-screen flex-1 flex-col lg:pl-64">
            {{-- Topbar --}}
            <header class="sticky top-0 z-30 border-b border-slate-200/80 bg-white/90 px-4 py-3 backdrop-blur sm:px-6 lg:px-8">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <nav class="text-xs text-slate-500" aria-label="Breadcrumb">
                            @hasSection('breadcrumbs')
                                @yield('breadcrumbs')
                            @else
                                <span>Dashboard</span>
                            @endif
                        </nav>
                    </div>
                    <div class="flex items-center gap-2 sm:gap-3">
                        <button type="button" class="rounded-full p-2 text-slate-600 hover:bg-slate-100" aria-label="Pengaturan">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        </button>
                        <button type="button" class="rounded-full p-2 text-slate-600 hover:bg-slate-100" aria-label="Bantuan">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" /></svg>
                        </button>
                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-nt-navy text-xs font-semibold text-white">AD</span>
                    </div>
                </div>
                @hasSection('page_heading')
                    <h1 class="mt-2 text-2xl font-bold text-nt-navy">@yield('page_heading')</h1>
                @endif
            </header>

            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
