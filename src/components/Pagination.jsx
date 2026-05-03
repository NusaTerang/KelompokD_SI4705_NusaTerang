import React from 'react';
import { ChevronLeft, ChevronRight } from 'lucide-react';

export default function Pagination() {
  return (
    <div className="flex items-center justify-center gap-2 mt-12 mb-16">
      <button className="w-10 h-10 flex items-center justify-center border border-gray-300 rounded-lg text-gray-500 hover:bg-gray-50 transition-colors">
        <ChevronLeft className="w-5 h-5" />
      </button>
      
      <button className="w-10 h-10 flex items-center justify-center bg-deep-navy text-white font-bold rounded-lg shadow-sm">
        1
      </button>
      
      <button className="w-10 h-10 flex items-center justify-center border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 transition-colors">
        2
      </button>
      
      <button className="w-10 h-10 flex items-center justify-center border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 transition-colors">
        3
      </button>
      
      <button className="w-10 h-10 flex items-center justify-center border border-gray-300 rounded-lg text-gray-500 hover:bg-gray-50 transition-colors">
        <ChevronRight className="w-5 h-5" />
      </button>
    </div>
  );
}
