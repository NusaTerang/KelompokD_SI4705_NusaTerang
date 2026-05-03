@extends('layouts.app')

@section('content')
    <x-hero />
    <x-filter-bar />

    <section class="w-full max-w-7xl mx-auto px-8 py-16 flex-grow">
        <div class="flex items-center justify-between mb-10">
            <h2 class="text-3xl font-heading font-extrabold text-gray-900">
                Pilih Proyek Anda
            </h2>
            <div class="flex items-center gap-2 text-gray-500 font-medium text-sm bg-white px-4 py-2 rounded-lg border border-gray-200">
                <span>Urutkan:</span>
                <select class="bg-transparent font-bold text-gray-900 border-none outline-none cursor-pointer focus:ring-0">
                    <option>Terbaru</option>
                    <option>Hampir Terpenuhi</option>
                    <option>Terpopuler</option>
                </select>
            </div>
        </div>

        @if($projects->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($projects as $project)
                    <x-project-card :project="$project" />
                @endforeach
            </div>

            <x-pagination :paginator="$projects" />
        @else
            <div class="py-20 text-center flex flex-col items-center justify-center bg-white rounded-2xl border border-dashed border-gray-300">
                <i data-lucide="inbox" class="w-16 h-16 text-gray-300 mb-4"></i>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Tidak ada proyek ditemukan</h3>
                <p class="text-gray-500 max-w-md">Belum ada proyek energi yang sesuai dengan pencarian atau filter Anda saat ini.</p>
            </div>
        @endif
    </section>

    <x-cta-section />
@endsection
