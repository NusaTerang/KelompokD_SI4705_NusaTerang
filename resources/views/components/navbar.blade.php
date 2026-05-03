<nav class="w-full h-[80px] bg-white border-b border-gray-200 flex items-center justify-between px-8 z-50 sticky top-0">
    <a href="{{ url('/') }}" class="flex items-center gap-2 text-deep-navy font-heading font-extrabold text-xl">
        <i data-lucide="sun" class="text-solar-gold fill-solar-gold w-6 h-6"></i>
        NusaTerang
    </a>
    
    <div class="flex gap-8 text-sm font-medium">
        @auth
            @if(Auth::user()->isAdmin())
                <a href="{{ route('dashboard') }}" class="{{ request()->is('admin*') ? 'text-deep-navy font-bold border-b-2 border-solar-gold pb-1' : 'text-[#4c4733] hover:text-deep-navy transition-colors' }}">Dashboard Admin</a>
                <a href="#" class="text-[#4c4733] hover:text-deep-navy transition-colors">Proyek Energi</a>
                <a href="{{ route('desa.daftar') }}" class="{{ request()->is('desa*') ? 'text-deep-navy font-bold border-b-2 border-solar-gold pb-1' : 'text-[#4c4733] hover:text-deep-navy transition-colors' }}">Data Desa</a>
                <a href="{{ url('/penyedia/daftar') }}" class="{{ request()->is('penyedia*') ? 'text-deep-navy font-bold border-b-2 border-solar-gold pb-1' : 'text-[#4c4733] hover:text-deep-navy transition-colors' }}">Penyedia Energi</a>
            @elseif(Auth::user()->isPenyedia())
                <a href="{{ route('dashboard') }}" class="{{ request()->is('penyedia/dashboard*') ? 'text-deep-navy font-bold border-b-2 border-solar-gold pb-1' : 'text-[#4c4733] hover:text-deep-navy transition-colors' }}">Dashboard</a>
                <a href="{{ url('/penyedia/daftar') }}" class="{{ request()->is('penyedia*') ? 'text-deep-navy font-bold border-b-2 border-solar-gold pb-1' : 'text-[#4c4733] hover:text-deep-navy transition-colors' }}">Penyedia Energi</a>
            @else
                <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'text-deep-navy font-bold border-b-2 border-solar-gold pb-1' : 'text-[#4c4733] hover:text-deep-navy transition-colors' }}">Beranda</a>
                <a href="#" class="text-[#4c4733] hover:text-deep-navy transition-colors">Proyek</a>
                <a href="{{ url('/penyedia/daftar') }}" class="{{ request()->is('penyedia*') ? 'text-deep-navy font-bold border-b-2 border-solar-gold pb-1' : 'text-[#4c4733] hover:text-deep-navy transition-colors' }}">Penyedia Energi</a>
            @endif
        @else
            <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'text-deep-navy font-bold border-b-2 border-solar-gold pb-1' : 'text-[#4c4733] hover:text-deep-navy transition-colors' }}">Beranda</a>
            <a href="#" class="text-[#4c4733] hover:text-deep-navy transition-colors">Proyek</a>
            <a href="{{ url('/penyedia/daftar') }}" class="{{ request()->is('penyedia*') ? 'text-deep-navy font-bold border-b-2 border-solar-gold pb-1' : 'text-[#4c4733] hover:text-deep-navy transition-colors' }}">Penyedia Energi</a>
            <a href="#" class="text-[#4c4733] hover:text-deep-navy transition-colors">Tentang</a>
        @endauth
    </div>

    <div class="flex items-center gap-6">
        @auth
            <a href="{{ route('dashboard') }}" class="text-sm font-bold text-deep-navy hover:text-solar-gold transition-colors">
                Hi, {{ Auth::user()->nama }}
            </a>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center border border-gray-300 text-gray-600 hover:bg-gray-200 transition-colors">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                </button>
            </form>
        @else
            <a href="{{ route('login') }}" class="bg-solar-gold hover:bg-yellow-500 text-deep-navy font-bold px-6 py-2 rounded-lg transition-colors shadow-sm">
                Masuk / Daftar
            </a>
        @endauth
    </div>
</nav>
