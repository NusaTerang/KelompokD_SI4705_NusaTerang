import React from 'react';
import { Search } from 'lucide-react';

export default function Hero() {
  return (
    <section className="w-full bg-deep-navy py-24 flex flex-col items-center justify-center relative overflow-hidden text-center px-4">
      <div className="absolute top-20 left-0 w-full h-[444px] bg-[url('https://images.unsplash.com/photo-1508514177221-188b1cf16e9d?q=80&w=2072&auto=format&fit=crop')] bg-cover opacity-10"></div>
      
      <div className="relative z-10 max-w-4xl flex flex-col items-center gap-6">
        <h1 className="text-white text-4xl md:text-6xl font-heading font-extrabold">
          Proyek Energi Aktif
        </h1>
        <p className="text-white/90 text-lg md:text-xl font-normal max-w-2xl leading-relaxed">
          Bersama wujudkan kemandirian energi di pelosok Nusantara. Pilih proyek berkelanjutan dan pantau dampak donasi Anda secara transparan.
        </p>

        <div className="mt-4 w-full max-w-2xl flex items-center bg-white rounded-xl p-2 shadow-2xl">
          <div className="pl-4 flex items-center text-gray-400">
            <Search className="w-5 h-5" />
          </div>
          <input 
            type="text" 
            placeholder="Cari nama desa atau wilayah..." 
            className="flex-1 bg-transparent border-none outline-none px-4 py-3 text-gray-700"
          />
          <button className="bg-deep-navy text-white font-bold px-8 py-3 rounded-lg hover:bg-blue-900 transition-colors">
            Cari
          </button>
        </div>
      </div>
    </section>
  );
}
