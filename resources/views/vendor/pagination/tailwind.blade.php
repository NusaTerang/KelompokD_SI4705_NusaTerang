@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-col items-center gap-4">

        {{-- Mobile --}}
        <div class="flex gap-2 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center px-4 py-2 text-sm font-headline font-medium text-outline bg-surface-container border border-outline-variant cursor-not-allowed rounded-lg">
                    &laquo; Sebelumnya
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-4 py-2 text-sm font-headline font-medium text-secondary bg-white border border-outline-variant rounded-lg hover:bg-surface-container transition-all">
                    &laquo; Sebelumnya
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-4 py-2 text-sm font-headline font-medium text-secondary bg-white border border-outline-variant rounded-lg hover:bg-surface-container transition-all">
                    Berikutnya &raquo;
                </a>
            @else
                <span class="inline-flex items-center px-4 py-2 text-sm font-headline font-medium text-outline bg-surface-container border border-outline-variant cursor-not-allowed rounded-lg">
                    Berikutnya &raquo;
                </span>
            @endif
        </div>

        {{-- Desktop --}}
        <div class="hidden sm:flex sm:flex-col sm:items-center sm:gap-4 w-full">

            {{-- Info teks --}}
            <p class="text-sm text-on-surface-variant font-body">
                Menampilkan
                @if ($paginator->firstItem())
                    <span class="font-bold text-on-surface">{{ $paginator->firstItem() }}</span>
                    &ndash;
                    <span class="font-bold text-on-surface">{{ $paginator->lastItem() }}</span>
                @else
                    {{ $paginator->count() }}
                @endif
                dari
                <span class="font-bold text-on-surface">{{ $paginator->total() }}</span>
                proyek
            </p>

            {{-- Page buttons --}}
            <div class="flex items-center gap-1">

                {{-- Prev --}}
                @if ($paginator->onFirstPage())
                    <span class="inline-flex items-center justify-center w-10 h-10 text-outline bg-surface-container border border-outline-variant cursor-not-allowed rounded-lg">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center justify-center w-10 h-10 text-secondary bg-white border border-outline-variant rounded-lg hover:bg-secondary hover:text-white transition-all" aria-label="{{ __('pagination.previous') }}">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </a>
                @endif

                {{-- Page numbers --}}
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="inline-flex items-center justify-center w-10 h-10 text-sm text-on-surface-variant font-body cursor-default">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="inline-flex items-center justify-center w-10 h-10 text-sm font-headline font-bold text-white bg-secondary border border-secondary rounded-lg shadow-md cursor-default">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="inline-flex items-center justify-center w-10 h-10 text-sm font-headline font-medium text-secondary bg-white border border-outline-variant rounded-lg hover:bg-secondary hover:text-white transition-all" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center justify-center w-10 h-10 text-secondary bg-white border border-outline-variant rounded-lg hover:bg-secondary hover:text-white transition-all" aria-label="{{ __('pagination.next') }}">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </a>
                @else
                    <span class="inline-flex items-center justify-center w-10 h-10 text-outline bg-surface-container border border-outline-variant cursor-not-allowed rounded-lg">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </span>
                @endif

            </div>
        </div>

    </nav>
@endif
