@props(['provider'])

@php
    $isActive = strtolower($provider->status) === 'aktif';
    // Fallback logo
    $logoUrl = $provider->foto_profil ? asset('storage/' . $provider->foto_profil) : 'https://images.unsplash.com/photo-1599930113854-d6d7fd521f10?w=150&h=150&fit=crop&q=80';
    $services = $provider->spesialisasi ? explode(',', str_replace('_', ' ', strtoupper($provider->spesialisasi))) : ['UMUM'];
@endphp

<div class="w-full bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col gap-4 hover:shadow-md transition-shadow">
    
    <!-- Top: Logo & Status -->
    <div class="flex justify-between items-start w-full">
        <div class="w-16 h-16 rounded-xl bg-gray-50 overflow-hidden shrink-0">
            <img 
                src="{{ $logoUrl }}" 
                alt="{{ $provider->nama }}" 
                class="w-full h-full object-cover"
            />
        </div>
        
        <div class="flex items-center gap-1.5 px-3 py-1 rounded-full {{ $isActive ? 'bg-green-50' : 'bg-gray-100' }}">
            <div class="w-1.5 h-1.5 rounded-full {{ $isActive ? 'bg-green-700' : 'bg-gray-500' }}"></div>
            <span class="text-xs font-bold tracking-wider {{ $isActive ? 'text-green-700' : 'text-gray-500' }}">
                {{ strtoupper($provider->status) }}
            </span>
        </div>
    </div>

    <!-- Name -->
    <div class="w-full py-1">
        <h3 class="text-xl font-heading font-semibold text-gray-900 leading-tight">
            {{ $provider->nama }}
        </h3>
    </div>

    <!-- Contact Info -->
    <div class="flex flex-col gap-2 w-full">
        <div class="flex items-center gap-2 text-sm text-[#4c4733]">
            <i data-lucide="map-pin" class="w-4 h-4 shrink-0"></i>
            <span class="truncate">{{ $provider->provinsi_operasi ?? 'Seluruh Indonesia' }}</span>
        </div>
        <div class="flex items-center gap-2 text-sm text-[#4c4733]">
            <i data-lucide="star" class="w-4 h-4 shrink-0 text-yellow-500"></i>
            <span class="truncate">Rating: {{ $provider->rating ?? 'N/A' }} / 5.0</span>
        </div>
    </div>

    <!-- Tags -->
    <div class="flex flex-wrap gap-2 w-full py-2">
        @foreach($services as $service)
            <div class="bg-[#ebeeed] px-3 py-1 rounded-lg">
                <span class="text-[#4c4733] text-xs font-semibold">{{ $service }}</span>
            </div>
        @endforeach
    </div>

    <!-- Actions -->
    <div class="flex flex-col gap-3 w-full mt-auto pt-2">
        <a href="#" class="w-full py-3 rounded-xl border-2 border-deep-navy text-deep-navy font-bold hover:bg-gray-50 transition-colors text-center block">
            Lihat Profil
        </a>
        
        @if($isActive)
            <a href="#" class="w-full py-3 rounded-xl bg-solar-gold text-[#544600] font-bold flex items-center justify-center gap-2 hover:bg-yellow-500 transition-colors shadow-sm block text-center">
                <i data-lucide="check-circle-2" class="w-4 h-4 inline"></i>
                Assign ke Proyek
            </a>
        @else
            <button class="w-full py-3 rounded-xl bg-[#ebeeed] text-[#7e7760] font-bold flex items-center justify-center gap-2 cursor-not-allowed">
                <i data-lucide="x-circle" class="w-4 h-4 inline"></i>
                Tidak Tersedia
            </button>
        @endif
    </div>

</div>
