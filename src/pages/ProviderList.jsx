import React from 'react';
import { Search, ChevronDown, PlusCircle } from 'lucide-react';
import ProviderCard from '../components/ProviderCard';
import Pagination from '../components/Pagination';

const DUMMY_PROVIDERS = [
  {
    id: 1,
    name: 'Solar Nusantara Utama',
    status: 'AKTIF',
    location: 'Jakarta Selatan, DKI Jakarta',
    email: 'info@solarnusantara.id',
    services: ['Panel Surya', 'Instalasi', '+2 lainnya'],
    logo: 'https://images.unsplash.com/photo-1599930113854-d6d7fd521f10?w=150&h=150&fit=crop&q=80'
  },
  {
    id: 2,
    name: 'EcoWatt Solutions',
    status: 'AKTIF',
    location: 'Surabaya, Jawa Timur',
    email: 'contact@ecowatt.co.id',
    services: ['Baterai LFP', 'Microgrid'],
    logo: 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=150&h=150&fit=crop&q=80'
  },
  {
    id: 3,
    name: 'Mandiri Energy Group',
    status: 'NONAKTIF',
    location: 'Bandung, Jawa Barat',
    email: 'support@mandirienergy.com',
    services: ['Konsultasi', 'Pemeliharaan'],
    logo: 'https://images.unsplash.com/photo-1560179707-f14e90ef3623?w=150&h=150&fit=crop&q=80'
  },
  {
    id: 4,
    name: 'Nusa Solar Lestari',
    status: 'AKTIF',
    location: 'Denpasar, Bali',
    email: 'admin@nusasolar.id',
    services: ['Solar Farm', 'Financing'],
    logo: 'https://images.unsplash.com/photo-1554200876-56c2f25224fa?w=150&h=150&fit=crop&q=80'
  },
  {
    id: 5,
    name: 'Infrasurya Global',
    status: 'AKTIF',
    location: 'Medan, Sumatera Utara',
    email: 'hello@infrasurya.com',
    services: ['Panel Surya', 'Monitoring'],
    logo: 'https://images.unsplash.com/photo-1513694203232-719a280e022f?w=150&h=150&fit=crop&q=80'
  },
  {
    id: 6,
    name: 'Mitra Energi Bangsa',
    status: 'AKTIF',
    location: 'Makassar, Sulawesi Selatan',
    email: 'corp@meb.id',
    services: ['Solar Hybrid', 'EPC Services'],
    logo: 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=150&h=150&fit=crop&q=80'
  }
];

export default function ProviderList() {
  return (
    <main className="flex-grow flex flex-col w-full relative">
      
      {/* Hero Section */}
      <section className="w-full bg-deep-navy pt-20 pb-32 flex flex-col items-center justify-center relative overflow-hidden text-center px-4">
        {/* Subtle background glow */}
        <div className="absolute top-10 left-1/2 -translate-x-1/2 w-full max-w-4xl h-[300px] bg-[radial-gradient(ellipse_at_top,_var(--color-solar-gold)_0%,_transparent_50%)] opacity-10"></div>
        
        <div className="relative z-10 max-w-3xl flex flex-col items-center gap-4">
          <h1 className="text-white text-4xl md:text-5xl font-heading font-extrabold tracking-tight">
            Penyedia Energi Terpercaya
          </h1>
          <p className="text-blue-100 text-lg font-normal max-w-2xl leading-relaxed">
            Mendukung transisi energi berkelanjutan melalui kemitraan strategis dengan penyedia infrastruktur panel surya dan solusi terbarukan terbaik di Indonesia.
          </p>
        </div>
      </section>

      {/* Main Content Area */}
      <section className="w-full max-w-7xl mx-auto px-4 -mt-16 relative z-20 flex flex-col gap-8 pb-12">
        
        {/* Filter & Search Bar */}
        <div className="w-full bg-white rounded-2xl shadow-lg border border-gray-100 p-6 lg:p-8 flex flex-col lg:flex-row items-center justify-between gap-6">
          
          {/* Search Input */}
          <div className="relative w-full lg:max-w-lg">
            <div className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
              <Search className="w-5 h-5" />
            </div>
            <input 
              type="text" 
              placeholder="Cari nama penyedia atau layanan..." 
              className="w-full bg-[#f1f4f3] rounded-xl py-3.5 pl-12 pr-4 outline-none text-gray-900 focus:ring-2 focus:ring-deep-navy transition-shadow"
            />
          </div>

          {/* Filters & Actions */}
          <div className="flex flex-col sm:flex-row items-center gap-4 w-full lg:w-auto">
            
            {/* Dropdown Semua Layanan */}
            <button className="flex items-center justify-between w-full sm:w-auto min-w-[180px] bg-[#f1f4f3] px-5 py-3.5 rounded-xl hover:bg-gray-200 transition-colors">
              <span className="text-[#181c1c] text-sm font-medium">Semua Layanan</span>
              <ChevronDown className="w-4 h-4 text-gray-500" />
            </button>

            {/* Toggle Aktif / Semua */}
            <div className="flex items-center bg-[#f1f4f3] p-1 rounded-xl w-full sm:w-auto shrink-0">
              <button className="flex-1 sm:flex-none px-6 py-2.5 bg-white text-deep-navy text-sm font-bold rounded-lg shadow-sm">
                Aktif
              </button>
              <button className="flex-1 sm:flex-none px-6 py-2.5 text-[#7e7760] text-sm font-medium rounded-lg hover:text-gray-900 transition-colors">
                Semua
              </button>
            </div>

            {/* Action Button */}
            <button className="flex items-center justify-center gap-2 w-full sm:w-auto bg-solar-gold text-[#544600] px-6 py-3.5 rounded-xl font-bold hover:bg-yellow-500 transition-colors shadow-sm shrink-0">
              <PlusCircle className="w-5 h-5" />
              Mulai Proyek Baru
            </button>

          </div>
        </div>

        {/* Provider Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {DUMMY_PROVIDERS.map(provider => (
            <ProviderCard key={provider.id} provider={provider} />
          ))}
        </div>

        {/* Pagination */}
        <div className="mt-8">
          <Pagination />
        </div>

      </section>

    </main>
  );
}
