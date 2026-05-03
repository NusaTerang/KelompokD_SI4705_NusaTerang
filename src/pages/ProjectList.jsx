import React from 'react';
import Hero from '../components/Hero';
import FilterBar from '../components/FilterBar';
import ProjectCard from '../components/ProjectCard';
import Pagination from '../components/Pagination';
import CTASection from '../components/CTASection';

const DUMMY_PROJECTS = [
  {
    id: 1,
    title: 'Desa Wae Rebo Mandiri',
    description: 'Pemasangan PLTS Terpusat 15kWp untuk mendukung penerangan dan pendidikan 150...',
    locationBadge: 'NTT, FLORES',
    image: 'https://images.unsplash.com/photo-1508514177221-188b1cf16e9d?w=800&auto=format&fit=crop&q=60',
    progress: 75,
    targetText: 'Rp 250jt',
    raisedText: 'Rp 187.500.000',
    daysLeft: 12
  },
  {
    id: 2,
    title: 'Mikrohidro Sungai Sawai',
    description: 'Pemanfaatan arus sungai untuk energi berkelanjutan bagi industri pengolahan sagu lokal.',
    locationBadge: 'MALUKU, SERAM',
    image: 'https://images.unsplash.com/photo-1437482078695-73f5ca6c96e2?w=800&auto=format&fit=crop&q=60',
    progress: 42,
    targetText: 'Rp 450jt',
    raisedText: 'Rp 189.000.000',
    daysLeft: 24
  },
  {
    id: 3,
    title: 'Angin Pesisir Sumba',
    description: 'Instalasi turbin angin skala kecil untuk mendukung pompa air bersih bagi petani pesisir.',
    locationBadge: 'NTB, SUMBA',
    image: 'https://images.unsplash.com/photo-1466611653911-95081537e5b7?w=800&auto=format&fit=crop&q=60',
    progress: 88,
    targetText: 'Rp 120jt',
    raisedText: 'Rp 105.600.000',
    daysLeft: 5
  },
  {
    id: 4,
    title: 'Terang Papua Pintar',
    description: 'Elektrifikasi sekolah dan pusat belajar komunitas di pedalaman Merauke menggunakan biomassa.',
    locationBadge: 'PAPUA, MERAUKE',
    image: 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?w=800&auto=format&fit=crop&q=60',
    progress: 15,
    targetText: 'Rp 600jt',
    raisedText: 'Rp 90.000.000',
    daysLeft: 45
  },
  {
    id: 5,
    title: 'Eco-Light Wakatobi',
    description: 'Penerangan jalan umum ramah lingkungan untuk keamanan nelayan saat melaut di malam hari.',
    locationBadge: 'SULAWESI, WAKATOBI',
    image: 'https://images.unsplash.com/photo-1498623116890-37e912163d5d?w=800&auto=format&fit=crop&q=60',
    progress: 60,
    targetText: 'Rp 180jt',
    raisedText: 'Rp 108.000.000',
    daysLeft: 18
  },
  {
    id: 6,
    title: 'Irigasi Surya Bali',
    description: 'Implementasi pompa air tenaga surya untuk kemandirian irigasi kelompok tani saat musim...',
    locationBadge: 'BALI, KARANGASEM',
    image: 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=800&auto=format&fit=crop&q=60',
    progress: 92,
    targetText: 'Rp 95jt',
    raisedText: 'Rp 87.400.000',
    daysLeft: 3
  }
];

export default function ProjectList() {
  return (
    <main className="flex-grow flex flex-col w-full">
      <Hero />
      <FilterBar />
      
      {/* Project Grid */}
      <section className="w-full max-w-7xl mx-auto px-4 py-12">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          {DUMMY_PROJECTS.map(project => (
            <ProjectCard key={project.id} project={project} />
          ))}
        </div>
        
        <Pagination />
      </section>

      <CTASection />
    </main>
  );
}
