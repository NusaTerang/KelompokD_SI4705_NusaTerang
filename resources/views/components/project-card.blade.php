@props(['project'])

@php
    $progress = $project->target_dana > 0 ? round(($project->dana_terkumpul / $project->target_dana) * 100) : 0;
    $daysLeft = $project->estimasi_selesai ? now()->diffInDays($project->estimasi_selesai, false) : 0;
    $daysLeft = $daysLeft > 0 ? $daysLeft : 0;
    
    // Get the first photo if available, otherwise a placeholder
    $imageUrl = $project->fotos->first() ? asset('storage/' . $project->fotos->first()->path) : 'https://images.unsplash.com/photo-1508514177221-188b1cf16e9d?w=800&auto=format&fit=crop&q=60';
    $locationBadge = $project->desa ? strtoupper($project->desa->provinsi . ', ' . $project->desa->nama_desa) : 'LOKASI TIDAK DIKETAHUI';
@endphp

<div class="w-full bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow flex flex-col group">
    
    <!-- Image & Badge -->
    <div class="relative h-48 w-full overflow-hidden">
        <img 
            src="{{ $imageUrl }}" 
            alt="{{ $project->judul }}" 
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
        />
        <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm shadow-sm rounded-full px-3 py-1.5 flex items-center gap-1.5">
            <i data-lucide="map-pin" class="w-3 h-3 text-deep-navy"></i>
            <span class="text-[10px] font-bold text-deep-navy tracking-wider uppercase">
                {{ $locationBadge }}
            </span>
        </div>
    </div>

    <!-- Content -->
    <div class="p-6 flex flex-col flex-grow">
        
        <h3 class="text-xl font-heading font-bold text-gray-900 mb-2 leading-tight">
            {{ $project->judul }}
        </h3>
        <p class="text-sm text-gray-600 leading-relaxed mb-6 flex-grow line-clamp-2">
            {{ $project->deskripsi }}
        </p>

        <!-- Progress -->
        <div class="mb-6">
            <div class="flex justify-between items-end mb-2">
                <span class="text-2xl font-bold text-deep-navy leading-none">{{ $progress }}%</span>
                <span class="text-xs font-medium text-gray-600">Target: Rp {{ number_format($project->target_dana, 0, ',', '.') }}</span>
            </div>
            <div class="w-full h-2.5 bg-gray-100 rounded-full overflow-hidden">
                <div 
                    class="h-full bg-solar-gold rounded-full transition-all duration-1000 ease-out"
                    style="width: {{ $progress > 100 ? 100 : $progress }}%"
                ></div>
            </div>
        </div>

        <!-- Stats -->
        <div class="flex justify-between items-start border-t border-gray-100 pt-4 mb-4">
            <div>
                <p class="text-[10px] font-bold text-gray-500 tracking-wider uppercase mb-1">TERKUMPUL</p>
                <p class="text-sm font-bold text-gray-900">Rp {{ number_format($project->dana_terkumpul, 0, ',', '.') }}</p>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-bold text-gray-500 tracking-wider uppercase mb-1">SISA HARI</p>
                <p class="text-sm font-bold text-gray-900">{{ $daysLeft }} Hari</p>
            </div>
        </div>

        <!-- Action -->
        <a href="{{ route('proyek.show', $project->id_proyek ?? $project->id) }}" class="w-full py-3 rounded-lg border-2 border-deep-navy text-deep-navy font-heading font-bold text-sm flex items-center justify-center gap-2 hover:bg-deep-navy hover:text-white transition-colors mt-auto">
            Lihat Detail
            <i data-lucide="arrow-right" class="w-4 h-4"></i>
        </a>

    </div>
</div>
