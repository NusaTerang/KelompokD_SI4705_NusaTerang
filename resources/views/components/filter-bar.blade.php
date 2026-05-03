<div class="w-full bg-white/90 backdrop-blur-md border-b border-gray-100 shadow-sm py-6 sticky top-[80px] z-40">
    <div class="max-w-7xl mx-auto px-8 flex flex-col md:flex-row items-center justify-between gap-4">
        
        <div class="flex items-center gap-3 overflow-x-auto w-full md:w-auto pb-2 md:pb-0 hide-scrollbar">
            <a href="{{ url()->current() }}" class="{{ !request('status') ? 'px-6 py-2 rounded-full bg-deep-navy text-white font-bold whitespace-nowrap shadow-md' : 'px-6 py-2 rounded-full border border-gray-300 text-gray-600 font-medium whitespace-nowrap hover:bg-gray-50 transition-colors' }}">
                Semua
            </a>
            <a href="{{ url()->current() }}?status=berlangsung" class="{{ request('status') == 'berlangsung' ? 'px-6 py-2 rounded-full bg-deep-navy text-white font-bold whitespace-nowrap shadow-md' : 'px-6 py-2 rounded-full border border-gray-300 text-gray-600 font-medium whitespace-nowrap hover:bg-gray-50 transition-colors' }}">
                Berlangsung
            </a>
            <a href="{{ url()->current() }}?status=terpenuhi" class="{{ request('status') == 'terpenuhi' ? 'px-6 py-2 rounded-full bg-deep-navy text-white font-bold whitespace-nowrap shadow-md' : 'px-6 py-2 rounded-full border border-gray-300 text-gray-600 font-medium whitespace-nowrap hover:bg-gray-50 transition-colors' }}">
                Terpenuhi
            </a>
            <a href="{{ url()->current() }}?status=selesai" class="{{ request('status') == 'selesai' ? 'px-6 py-2 rounded-full bg-deep-navy text-white font-bold whitespace-nowrap shadow-md' : 'px-6 py-2 rounded-full border border-gray-300 text-gray-600 font-medium whitespace-nowrap hover:bg-gray-50 transition-colors' }}">
                Selesai
            </a>
        </div>

        <div class="flex items-center gap-4 w-full md:w-auto shrink-0">
            <span class="text-gray-600 font-medium text-sm">Filter wilayah:</span>
            <div class="flex items-center gap-2 bg-gray-100 px-4 py-2 rounded-lg cursor-pointer hover:bg-gray-200 transition-colors">
                <span class="text-gray-900 text-sm font-normal">Seluruh Indonesia</span>
                <i data-lucide="chevron-down" class="w-4 h-4 text-gray-600"></i>
            </div>
        </div>

    </div>
</div>
