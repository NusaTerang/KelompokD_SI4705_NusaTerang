import React from 'react';
import { Sun } from 'lucide-react';

export default function Footer() {
  return (
    <footer className="w-full bg-deep-navy text-white pt-16 pb-8 px-8">
      <div className="max-w-7xl mx-auto flex flex-col md:flex-row justify-between gap-12 mb-12">
        
        <div className="max-w-sm">
          <div className="flex items-center gap-2 font-heading font-extrabold text-2xl mb-6">
            <Sun className="text-solar-gold fill-solar-gold w-8 h-8" />
            NusaTerang
          </div>
          <p className="text-gray-300 text-sm leading-relaxed">
            Platform crowdfunding energi terbarukan pertama di Indonesia yang fokus pada pemberdayaan desa tertinggal, terdepan, dan terluar.
          </p>
          <div className="flex gap-4 mt-6">
            <div className="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center cursor-pointer hover:bg-white/20 transition-colors"></div>
            <div className="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center cursor-pointer hover:bg-white/20 transition-colors"></div>
            <div className="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center cursor-pointer hover:bg-white/20 transition-colors"></div>
          </div>
        </div>

        <div className="flex flex-col md:flex-row gap-16">
          <div>
            <h4 className="font-bold mb-6">Tautan Cepat</h4>
            <ul className="space-y-4 text-sm text-gray-300">
              <li><a href="#" className="hover:text-white transition-colors">Tentang Kami</a></li>
              <li><a href="#" className="hover:text-white transition-colors">Cara Kerja</a></li>
              <li><a href="#" className="hover:text-white transition-colors">Daftar Proyek</a></li>
              <li><a href="#" className="hover:text-white transition-colors">Penyedia Energi</a></li>
            </ul>
          </div>
          
          <div>
            <h4 className="font-bold mb-6">Bantuan</h4>
            <ul className="space-y-4 text-sm text-gray-300">
              <li><a href="#" className="hover:text-white transition-colors">Pusat Bantuan</a></li>
              <li><a href="#" className="hover:text-white transition-colors">Kontak Kami</a></li>
              <li><a href="#" className="hover:text-white transition-colors">Kebijakan Privasi</a></li>
              <li><a href="#" className="hover:text-white transition-colors">Syarat & Ketentuan</a></li>
            </ul>
          </div>
        </div>
        
      </div>

      <div className="max-w-7xl mx-auto border-t border-white/10 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-gray-400">
        <p>© 2026 NusaTerang. Seluruh hak cipta dilindungi.</p>
        <p>Terdaftar dan diawasi oleh Otoritas Jasa Keuangan.</p>
      </div>
    </footer>
  );
}
