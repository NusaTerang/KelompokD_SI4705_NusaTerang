<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Profil') — NusaTerang</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head')
</head>
<body class="bg-background font-body text-on-background min-h-screen">

    <!-- Navbar -->
    <nav class="fixed top-0 w-full flex justify-between items-center px-8 h-20 bg-white/80 backdrop-blur-md shadow-sm z-50">
        <a href="{{ url('/') }}" class="flex items-center gap-2 text-slate-900 flex-shrink-0">
            <span class="material-symbols-outlined text-primary-container" style="font-variation-settings: 'FILL' 1;">wb_sunny</span>
            <span class="font-headline font-extrabold tracking-tight text-2xl">NusaTerang</span>
        </a>

        <div class="hidden md:flex items-center gap-8 font-headline font-semibold text-sm absolute left-1/2 -translate-x-1/2">
            @auth
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('dashboard') }}" class="{{ request()->is('admin*') ? 'text-primary-container font-bold border-b-2 border-primary-container pb-1' : 'text-slate-600 hover:text-primary-container transition-colors' }}">Dashboard Admin</a>
                    <a href="{{ url('/proyek') }}" class="{{ request()->is('proyek*') ? 'text-primary-container font-bold border-b-2 border-primary-container pb-1' : 'text-slate-600 hover:text-primary-container transition-colors' }}">Proyek Energi</a>
                    <a href="{{ route('desa.daftar') }}" class="{{ request()->is('desa*') ? 'text-primary-container font-bold border-b-2 border-primary-container pb-1' : 'text-slate-600 hover:text-primary-container transition-colors' }}">Data Desa</a>
                    <a href="{{ url('/penyedia/daftar') }}" class="{{ request()->is('penyedia*') ? 'text-primary-container font-bold border-b-2 border-primary-container pb-1' : 'text-slate-600 hover:text-primary-container transition-colors' }}">Penyedia Energi</a>
                @elseif(Auth::user()->isPenyedia())
                    <a href="{{ route('dashboard') }}" class="{{ request()->is('penyedia/dashboard*') ? 'text-primary-container font-bold border-b-2 border-primary-container pb-1' : 'text-slate-600 hover:text-primary-container transition-colors' }}">Dashboard</a>
                    <a href="{{ url('/penyedia/daftar') }}" class="{{ request()->is('penyedia*') ? 'text-primary-container font-bold border-b-2 border-primary-container pb-1' : 'text-slate-600 hover:text-primary-container transition-colors' }}">Penyedia Energi</a>
                @else
                    <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'text-primary-container font-bold border-b-2 border-primary-container pb-1' : 'text-slate-600 hover:text-primary-container transition-colors' }}">Beranda</a>
                    <a href="{{ url('/proyek') }}" class="{{ request()->is('proyek*') ? 'text-primary-container font-bold border-b-2 border-primary-container pb-1' : 'text-slate-600 hover:text-primary-container transition-colors' }}">Proyek</a>
                    <a href="{{ url('/penyedia/daftar') }}" class="{{ request()->is('penyedia*') ? 'text-primary-container font-bold border-b-2 border-primary-container pb-1' : 'text-slate-600 hover:text-primary-container transition-colors' }}">Penyedia Energi</a>
                @endif
            @else
                <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'text-primary-container font-bold border-b-2 border-primary-container pb-1' : 'text-slate-600 hover:text-primary-container transition-colors' }}">Beranda</a>
                <a href="{{ url('/proyek') }}" class="{{ request()->is('proyek*') ? 'text-primary-container font-bold border-b-2 border-primary-container pb-1' : 'text-slate-600 hover:text-primary-container transition-colors' }}">Proyek</a>
                <a href="{{ url('/penyedia/daftar') }}" class="{{ request()->is('penyedia*') ? 'text-primary-container font-bold border-b-2 border-primary-container pb-1' : 'text-slate-600 hover:text-primary-container transition-colors' }}">Penyedia Energi</a>
                <a href="#" class="text-slate-600 hover:text-primary-container transition-colors">Tentang</a>
            @endauth
        </div>

        <div class="flex items-center gap-3 flex-shrink-0">
            @auth
                @php($unreadNotifications = Auth::user()->unreadNotifications()->count())

                {{-- Notification bell --}}
                <a href="{{ route('notifications.index') }}" class="relative w-9 h-9 rounded-full bg-surface-container flex items-center justify-center hover:bg-surface-container-high transition-colors flex-shrink-0" aria-label="Notifikasi">
                    <span class="material-symbols-outlined text-[18px] text-on-surface-variant">notifications</span>
                    @if($unreadNotifications > 0)
                        <span class="absolute -right-1 -top-1 min-w-5 rounded-full bg-red-600 px-1.5 py-0.5 text-[10px] font-bold leading-none text-white">{{ $unreadNotifications > 99 ? '99+' : $unreadNotifications }}</span>
                    @endif
                </a>

                {{-- Profile dropdown --}}
                <div class="relative" id="nt-profile-menu">
                    <button onclick="ntToggleProfile(event)" class="w-9 h-9 rounded-full bg-primary-container flex items-center justify-center hover:opacity-80 transition-all flex-shrink-0 font-bold text-sm text-on-primary-fixed select-none" aria-label="Menu Profil">
                        {{ strtoupper(mb_substr(Auth::user()->nama, 0, 1)) }}
                    </button>

                    <div id="nt-profile-dropdown" class="hidden absolute right-0 top-12 w-72 bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden z-[100]">

                        {{-- User header --}}
                        <div class="px-4 py-4 bg-slate-50 border-b border-slate-100">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-full bg-primary-container flex items-center justify-center flex-shrink-0 font-bold text-base text-on-primary-fixed">
                                    {{ strtoupper(mb_substr(Auth::user()->nama, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-slate-900 truncate">{{ Auth::user()->nama }}</p>
                                    <p class="text-xs text-slate-500 truncate">{{ Auth::user()->email }}</p>
                                </div>
                            </div>

                            @if(Auth::user()->isDonatur())
                                <a href="{{ route('donatur.saldo') }}" onclick="ntCloseProfile()" class="mt-3 flex items-center justify-between px-3 py-2.5 rounded-xl bg-solar-gold/20 hover:bg-solar-gold/30 transition-colors {{ request()->is('donatur/saldo') ? 'ring-2 ring-solar-gold' : '' }}">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[16px] text-deep-navy">account_balance_wallet</span>
                                        <span class="text-xs font-semibold text-deep-navy">Saldo Saya</span>
                                    </div>
                                    <span class="text-sm font-bold text-deep-navy">Rp {{ number_format(Auth::user()->saldo, 0, ',', '.') }}</span>
                                </a>
                            @endif
                        </div>

                        {{-- Menu links --}}
                        <div class="py-1.5">
                            <a href="{{ route('profil.edit') }}" onclick="ntCloseProfile()" class="flex items-center gap-3 px-4 py-3 text-sm text-slate-700 hover:bg-slate-50 transition-colors {{ request()->routeIs('profil.*') ? 'bg-slate-50 font-semibold' : '' }}">
                                <span class="material-symbols-outlined text-[18px] text-slate-400">person</span>
                                Profil Saya
                            </a>
                            @if(Auth::user()->isAdmin() || Auth::user()->isPenyedia())
                                <a href="{{ route('dashboard') }}" onclick="ntCloseProfile()" class="flex items-center gap-3 px-4 py-3 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                                    <span class="material-symbols-outlined text-[18px] text-slate-400">dashboard</span>
                                    Dashboard
                                </a>
                            @endif
                        </div>

                        {{-- Logout --}}
                        <div class="border-t border-slate-100 py-1.5">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center gap-3 w-full px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">logout</span>
                                    Keluar
                                </button>
                            </form>
                        </div>

                    </div>
                </div>

            @else
                <a href="{{ route('login') }}" class="bg-primary-container text-on-primary-fixed px-6 py-2.5 rounded-lg font-headline font-bold text-sm hover:opacity-90 transition-all active:scale-95 whitespace-nowrap">
                    Mulai Donasi
                </a>
            @endauth
        </div>
    </nav>

    <script>
        function ntToggleProfile(e) {
            e.stopPropagation();
            document.getElementById('nt-profile-dropdown').classList.toggle('hidden');
        }
        function ntCloseProfile() {
            document.getElementById('nt-profile-dropdown').classList.add('hidden');
        }
        document.addEventListener('click', function(e) {
            var menu = document.getElementById('nt-profile-menu');
            if (menu && !menu.contains(e.target)) ntCloseProfile();
        });
    </script>

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
            <p>&copy; {{ date('Y') }} NusaTerang. Seluruh hak cipta dilindungi.</p>
            <p>Terdaftar dan diawasi oleh Otoritas Jasa Keuangan.</p>
        </div>
    </footer>

    @stack('scripts')

</body>
</html>
