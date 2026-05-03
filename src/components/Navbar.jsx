import React from 'react';
import { Sun } from 'lucide-react';

export default function Navbar() {
  return (
    <nav className="w-full h-[80px] bg-white border-b border-gray-200 flex items-center justify-between px-8 z-50 sticky top-0">
      <div className="flex items-center gap-2 text-deep-navy font-heading font-extrabold text-xl">
        <Sun className="text-solar-gold fill-solar-gold w-6 h-6" />
        NusaTerang
      </div>
      
      <div className="flex gap-8 text-sm font-medium text-[#4c4733]">
        <a href="#" className="hover:text-deep-navy transition-colors">Beranda</a>
        <a href="#" className="text-deep-navy font-bold border-b-2 border-solar-gold pb-1">Proyek</a>
        <a href="#" className="hover:text-deep-navy transition-colors">Penyedia Energi</a>
        <a href="#" className="hover:text-deep-navy transition-colors">Tentang</a>
      </div>

      <button className="bg-solar-gold hover:bg-yellow-500 text-deep-navy font-bold px-6 py-2 rounded-lg transition-colors shadow-sm">
        Mulai Donasi
      </button>
    </nav>
  );
}
