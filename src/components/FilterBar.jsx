import React from 'react';
import { ChevronDown } from 'lucide-react';

export default function FilterBar() {
  return (
    <div className="w-full bg-white/90 backdrop-blur-md border-b border-gray-100 shadow-sm py-6 sticky top-[80px] z-40">
      <div className="max-w-7xl mx-auto px-8 flex flex-col md:flex-row items-center justify-between gap-4">
        
        <div className="flex items-center gap-3 overflow-x-auto w-full md:w-auto pb-2 md:pb-0 hide-scrollbar">
          <button className="px-6 py-2 rounded-full border border-gray-300 text-gray-600 font-medium whitespace-nowrap hover:bg-gray-50 transition-colors">
            Semua
          </button>
          <button className="px-6 py-2 rounded-full bg-deep-navy text-white font-bold whitespace-nowrap shadow-md">
            Berlangsung
          </button>
          <button className="px-6 py-2 rounded-full border border-gray-300 text-gray-600 font-medium whitespace-nowrap hover:bg-gray-50 transition-colors">
            Terpenuhi
          </button>
          <button className="px-6 py-2 rounded-full border border-gray-300 text-gray-600 font-medium whitespace-nowrap hover:bg-gray-50 transition-colors">
            Selesai
          </button>
        </div>

        <div className="flex items-center gap-4 w-full md:w-auto shrink-0">
          <span className="text-gray-600 font-medium text-sm">Filter wilayah:</span>
          <div className="flex items-center gap-2 bg-gray-100 px-4 py-2 rounded-lg cursor-pointer hover:bg-gray-200 transition-colors">
            <span className="text-gray-900 text-sm font-normal">Seluruh Indonesia</span>
            <ChevronDown className="w-4 h-4 text-gray-600" />
          </div>
        </div>

      </div>
    </div>
  );
}
