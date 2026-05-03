@props(['paginator'])

@if ($paginator->hasPages())
    <div class="flex items-center justify-center gap-2 mt-12 mb-16">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="w-10 h-10 flex items-center justify-center border border-gray-300 rounded-lg text-gray-300">
                <i data-lucide="chevron-left" class="w-5 h-5"></i>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="w-10 h-10 flex items-center justify-center border border-gray-300 rounded-lg text-gray-500 hover:bg-gray-50 transition-colors">
                <i data-lucide="chevron-left" class="w-5 h-5"></i>
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($paginator->elements() as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="w-10 h-10 flex items-center justify-center text-gray-500">{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="w-10 h-10 flex items-center justify-center bg-deep-navy text-white font-bold rounded-lg shadow-sm">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" class="w-10 h-10 flex items-center justify-center border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 transition-colors">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="w-10 h-10 flex items-center justify-center border border-gray-300 rounded-lg text-gray-500 hover:bg-gray-50 transition-colors">
                <i data-lucide="chevron-right" class="w-5 h-5"></i>
            </a>
        @else
            <span class="w-10 h-10 flex items-center justify-center border border-gray-300 rounded-lg text-gray-300">
                <i data-lucide="chevron-right" class="w-5 h-5"></i>
            </span>
        @endif
    </div>
@endif
