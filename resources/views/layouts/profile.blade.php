<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Profil') — NusaTerang</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-800 antialiased">
    <header class="sticky top-0 z-50 border-b border-slate-200/80 bg-white/95 backdrop-blur">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-4 px-4 py-3 sm:px-6">
            <a href="{{ url('/') }}" class="text-xl font-bold text-nt-navy">NusaTerang</a>
            <nav class="flex flex-1 flex-wrap items-center justify-center gap-6 text-sm font-medium text-slate-600">
                <a href="#" class="hover:text-nt-navy">Dashboard</a>
                <a href="#" class="hover:text-nt-navy">Proyek Energi</a>
                <a href="{{ route('desa.kelola') }}" class="hover:text-nt-navy">Data Desa</a>
                <a href="#" class="hover:text-nt-navy">Donasi</a>
            </nav>
            <div class="flex items-center gap-3">
                @auth
                    <button type="button" class="rounded-full p-2 text-slate-600 hover:bg-slate-100" aria-label="Notifikasi">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0" /></svg>
                    </button>
                    <a href="{{ route('profil.edit') }}" class="text-sm font-semibold {{ request()->routeIs('profil.*') ? 'border-b-2 border-nt-accent text-nt-navy' : 'text-slate-600 hover:text-nt-navy' }}">Profil</a>
                    <form action="{{ route('logout') }}" method="post" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-slate-500 hover:text-nt-navy">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="rounded-xl bg-nt-accent px-4 py-2 text-sm font-bold text-nt-navy hover:bg-nt-accent-hover">Masuk</a>
                @endauth
            </div>
        </div>
    </header>

    @yield('content')

    <footer class="mt-16 border-t border-slate-200 bg-white py-10">
        <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-6 px-4 sm:flex-row sm:px-6">
            <span class="text-lg font-bold text-nt-navy/70">NusaTerang</span>
            <nav class="flex flex-wrap justify-center gap-6 text-sm text-slate-500">
                <a href="#" class="hover:text-nt-navy">Kebijakan Privasi</a>
                <a href="#" class="hover:text-nt-navy">Syarat &amp; Ketentuan</a>
                <a href="#" class="hover:text-nt-navy">Pusat Bantuan</a>
            </nav>
            <p class="text-center text-xs text-slate-400 sm:text-right">© {{ date('Y') }} NusaTerang. Membangun masa depan bertenaga surya.</p>
        </div>
    </footer>
    @stack('scripts')
</body>
</html>
