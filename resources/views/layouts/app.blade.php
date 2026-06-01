<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NusaTerang</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background font-body text-on-background min-h-screen">

    <!-- Navbar -->
    <nav class="fixed top-0 w-full flex justify-between items-center px-8 h-20 bg-white/80 backdrop-blur-md shadow-sm z-50">
        <a href="{{ url('/') }}" class="flex items-center gap-2 text-slate-900">
            <span class="material-symbols-outlined text-primary-container" style="font-variation-settings: 'FILL' 1;">wb_sunny</span>
            <span class="font-headline font-extrabold tracking-tight text-2xl">NusaTerang</span>
        </a>

        <div class="hidden md:flex items-center gap-8 font-headline font-semibold text-sm">
            <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'text-primary-container font-bold border-b-2 border-primary-container pb-1' : 'text-slate-600 hover:text-primary-container transition-colors' }}">Beranda</a>
            <a href="{{ url('/proyek') }}" class="{{ request()->is('proyek*') ? 'text-primary-container font-bold border-b-2 border-primary-container pb-1' : 'text-slate-600 hover:text-primary-container transition-colors' }}">Proyek</a>
            <a href="{{ url('/penyedia/daftar') }}" class="{{ request()->is('penyedia*') ? 'text-primary-container font-bold border-b-2 border-primary-container pb-1' : 'text-slate-600 hover:text-primary-container transition-colors' }}">Penyedia Energi</a>
            <a href="#" class="text-slate-600 hover:text-primary-container transition-colors">Tentang</a>
        </div>

        <div class="flex items-center gap-4">
            @auth
                <span class="text-sm font-semibold text-secondary">Hi, {{ Auth::user()->nama }}</span>
                <a href="{{ route('dashboard') }}" class="text-sm font-bold text-secondary hover:text-on-secondary-container transition-colors">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="w-9 h-9 rounded-full bg-surface-container flex items-center justify-center hover:bg-surface-container-high transition-colors">
                        <span class="material-symbols-outlined text-[18px] text-on-surface-variant">logout</span>
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="bg-primary-container text-on-primary-fixed px-6 py-2.5 rounded-lg font-headline font-bold text-sm hover:opacity-90 transition-all active:scale-95">
                    Mulai Donasi
                </a>
            @endauth
        </div>
    </nav>

    <main class="pt-20 flex flex-col w-full">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-secondary text-white py-16 px-8">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-12">

            <div class="col-span-1 md:col-span-2">
                <div class="flex items-center gap-2 mb-6">
                    <span class="material-symbols-outlined text-primary-container text-3xl" style="font-variation-settings: 'FILL' 1;">wb_sunny</span>
                    <span class="font-headline font-extrabold tracking-tight text-2xl">NusaTerang</span>
                </div>
                <p class="text-white/70 max-w-sm mb-8 font-body text-sm leading-relaxed">
                    Platform crowdfunding energi terbarukan pertama di Indonesia yang fokus pada pemberdayaan desa tertinggal, terdepan, dan terluar.
                </p>
                <div class="flex gap-4">
                    <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-primary-container hover:text-secondary transition-all">
                        <span class="material-symbols-outlined text-[18px]">social_leaderboard</span>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-primary-container hover:text-secondary transition-all">
                        <span class="material-symbols-outlined text-[18px]">camera</span>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-primary-container hover:text-secondary transition-all">
                        <span class="material-symbols-outlined text-[18px]">alternate_email</span>
                    </a>
                </div>
            </div>

            <div>
                <h4 class="font-headline font-bold text-lg mb-6">Tautan Cepat</h4>
                <ul class="flex flex-col gap-4 text-white/70 font-body text-sm">
                    <li><a href="#" class="hover:text-primary-container transition-colors">Tentang Kami</a></li>
                    <li><a href="#" class="hover:text-primary-container transition-colors">Cara Kerja</a></li>
                    <li><a href="{{ url('/') }}" class="hover:text-primary-container transition-colors">Daftar Proyek</a></li>
                    <li><a href="{{ url('/penyedia/daftar') }}" class="hover:text-primary-container transition-colors">Penyedia Energi</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-headline font-bold text-lg mb-6">Bantuan</h4>
                <ul class="flex flex-col gap-4 text-white/70 font-body text-sm">
                    <li><a href="#" class="hover:text-primary-container transition-colors">Pusat Bantuan</a></li>
                    <li><a href="#" class="hover:text-primary-container transition-colors">Kontak Kami</a></li>
                    <li><a href="#" class="hover:text-primary-container transition-colors">Kebijakan Privasi</a></li>
                    <li><a href="#" class="hover:text-primary-container transition-colors">Syarat &amp; Ketentuan</a></li>
                </ul>
            </div>

        </div>

        <div class="max-w-7xl mx-auto mt-16 pt-8 border-t border-white/10 flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-white/40">
            <p>&copy; 2024 NusaTerang. Seluruh hak cipta dilindungi.</p>
            <p>Terdaftar dan diawasi oleh Otoritas Jasa Keuangan.</p>
        </div>
    </footer>

</body>
</html>
