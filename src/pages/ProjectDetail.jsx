import React, { useEffect } from 'react';
import { MapPin, ShieldCheck, Leaf, Users } from 'lucide-react';
import { useParams } from 'react-router-dom';

export default function ProjectDetail() {
  const { id } = useParams();

  // Scroll to top when loaded
  useEffect(() => {
    window.scrollTo(0, 0);
  }, []);

  return (
    <main className="w-full max-w-[1216px] mx-auto px-4 py-12 flex flex-col lg:flex-row gap-12 items-start justify-start flex-grow">
      
      {/* Left Column - Project Info */}
      <div className="w-full lg:max-w-[701px] flex flex-col gap-8 flex-1">
        
        {/* Header Image */}
        <div className="w-full relative rounded-xl overflow-hidden shadow-md h-[300px] flex flex-col justify-center">
          <img
            src="https://images.unsplash.com/photo-1508514177221-188b1cf16e9d?w=800&auto=format&fit=crop&q=60"
            alt="panel surya di desa terpencil"
            className="w-full h-full object-cover"
          />
          <div className="absolute top-6 left-6 bg-solar-gold px-4 py-1.5 rounded-full shadow-sm">
            <span className="text-[#6d5b00] text-xs font-bold font-body tracking-wider uppercase">
              SEDANG BERJALAN
            </span>
          </div>
        </div>

        {/* Title and Location */}
        <div className="flex flex-col gap-3">
          <h1 className="text-gray-900 text-3xl md:text-4xl font-heading font-semibold leading-tight">
            Elektrifikasi Surya Desa Wae Rebo
          </h1>
          <div className="flex items-center gap-2">
            <MapPin className="w-5 h-5 text-gray-500" />
            <span className="text-[#4c4733] text-base font-medium">Nusa Tenggara Timur, Indonesia</span>
          </div>
        </div>

        {/* Stats Grid */}
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 w-full">
          <div className="bg-[#f1f4f3] p-4 rounded-xl border-l-4 border-[#6f5d00] flex flex-col gap-1">
            <p className="text-[#4c4733] text-xs font-bold uppercase tracking-wide">TERKUMPUL</p>
            <p className="text-[#181c1c] text-lg font-bold">Rp 842jt</p>
          </div>
          <div className="bg-[#f1f4f3] p-4 rounded-xl border-l-4 border-[#cfc6ac] flex flex-col gap-1">
            <p className="text-[#4c4733] text-xs font-bold uppercase tracking-wide">TARGET</p>
            <p className="text-[#181c1c] text-lg font-bold">Rp 1.2M</p>
          </div>
          <div className="bg-[#f1f4f3] p-4 rounded-xl border-l-4 border-[#92c1fe] flex flex-col gap-1">
            <p className="text-[#4c4733] text-xs font-bold uppercase tracking-wide">DONATUR</p>
            <p className="text-[#181c1c] text-lg font-bold">1,248</p>
          </div>
          <div className="bg-[#f1f4f3] p-4 rounded-xl border-l-4 border-[#72ef99] flex flex-col gap-1">
            <p className="text-[#4c4733] text-xs font-bold uppercase tracking-wide">SISA HARI</p>
            <p className="text-[#181c1c] text-lg font-bold">12 Hari</p>
          </div>
        </div>

        {/* Progress Bar */}
        <div className="flex flex-col gap-2 w-full">
          <div className="flex justify-between items-end">
            <p className="text-[#4c4733] text-sm font-bold">Kemajuan Pendanaan</p>
            <p className="text-[#6f5d00] text-2xl font-bold">70%</p>
          </div>
          <div className="w-full h-4 bg-gray-200 rounded-full overflow-hidden">
            <div className="h-full bg-solar-gold rounded-full" style={{ width: '70%' }}></div>
          </div>
        </div>

        {/* About Project */}
        <div className="flex flex-col gap-4 w-full">
          <h2 className="text-[#001c37] text-2xl font-heading font-semibold">Tentang Proyek</h2>
          <div className="text-[#4c4733] text-lg leading-relaxed flex flex-col gap-4">
            <p>
              Proyek ini bertujuan untuk menyediakan akses energi bersih bagi lebih dari 300 kepala keluarga di Desa Wae Rebo. Dengan pemasangan sistem mikro-grid tenaga surya, warga desa tidak lagi bergantung pada generator diesel yang mahal dan mencemari lingkungan.
            </p>
            <p>
              Selain penerangan rumah, energi ini akan dialokasikan untuk mendukung fasilitas pendidikan dan pengolahan hasil tani kopi lokal, meningkatkan nilai ekonomi desa secara berkelanjutan.
            </p>
          </div>
        </div>

        {/* Timeline */}
        <div className="flex flex-col gap-6 w-full py-6">
          <h2 className="text-[#001c37] text-2xl font-heading font-semibold">Update Progress</h2>
          
          <div className="flex flex-col border-l-2 border-[#e6e9e8] ml-4 pl-8 relative gap-8">
            
            {/* Timeline Item 1 */}
            <div className="relative bg-[#f1f4f3] rounded-xl p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 shadow-sm w-full">
              {/* Timeline Dot */}
              <div className="absolute -left-[43px] top-1/2 -translate-y-1/2 w-5 h-5 rounded-full bg-[#006d37] border-4 border-[#f7faf9]"></div>
              
              <div className="flex flex-col gap-1">
                <p className="text-[#006d37] text-xs font-bold uppercase">15 OKT 2023</p>
                <p className="text-[#181c1c] text-lg font-bold">Pengiriman Panel Tahap I</p>
                <p className="text-[#4c4733] text-sm leading-relaxed mt-1">
                  Logistik telah sampai di pelabuhan Labuan Bajo dan siap diberangkatkan ke lokasi.
                </p>
              </div>
              <div className="bg-[#72ef99] px-4 py-2 rounded-full flex flex-col items-center justify-center shrink-0">
                <p className="text-[#006b36] text-xs font-bold leading-none">100%</p>
                <p className="text-[#006b36] text-xs font-bold">Selesai</p>
              </div>
            </div>

            {/* Timeline Item 2 */}
            <div className="relative bg-[#f1f4f3] rounded-xl p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 shadow-sm w-full">
              {/* Timeline Dot */}
              <div className="absolute -left-[43px] top-1/2 -translate-y-1/2 w-5 h-5 rounded-full bg-[#6f5d00] border-4 border-[#f7faf9]"></div>
              
              <div className="flex flex-col gap-1">
                <p className="text-[#6f5d00] text-xs font-bold uppercase">22 OKT 2023</p>
                <p className="text-[#181c1c] text-lg font-bold">Persiapan Fondasi Lahan</p>
                <p className="text-[#4c4733] text-sm leading-relaxed mt-1">
                  Tim teknis sedang melakukan perataan lahan dan pengecoran dudukan modul surya.
                </p>
              </div>
              <div className="bg-[#ffe16a] px-4 py-2 rounded-full flex flex-col items-center justify-center shrink-0">
                <p className="text-[#221b00] text-xs font-bold leading-none">45%</p>
                <p className="text-[#221b00] text-xs font-bold">Berjalan</p>
              </div>
            </div>

          </div>
        </div>

      </div>

      {/* Right Column - Donation Card */}
      <div className="w-full lg:max-w-[467px] flex flex-col sticky top-24">
        <div className="bg-white rounded-2xl p-8 border border-gray-100 shadow-xl flex flex-col gap-6 w-full">
          
          <h2 className="text-[#001c37] text-2xl font-heading font-semibold">Dukung Proyek Ini</h2>
          
          <div className="flex flex-col gap-2">
            <div className="flex justify-between items-center">
              <p className="text-[#4c4733] text-xs font-bold uppercase">TERKUMPUL</p>
              <p className="text-[#006d37] text-3xl font-bold">Rp 842.500.000</p>
            </div>
            <div className="w-full h-2 bg-gray-200 rounded-full overflow-hidden my-1">
              <div className="h-full bg-solar-gold rounded-full" style={{ width: '70%' }}></div>
            </div>
            <div className="flex gap-1 text-xs">
              <span className="text-[#4c4733]">Dari target</span>
              <span className="text-[#4c4733] font-bold">Rp 1.200.000.000</span>
            </div>
          </div>

          <div className="grid grid-cols-2 gap-3 w-full">
            <button className="py-3 rounded-xl border border-[#cfc6ac] text-[#181c1c] font-bold hover:bg-gray-50 transition-colors">Rp 50k</button>
            <button className="py-3 rounded-xl border border-[#cfc6ac] text-[#181c1c] font-bold hover:bg-gray-50 transition-colors">Rp 100k</button>
            <button className="py-3 rounded-xl border border-[#cfc6ac] text-[#181c1c] font-bold hover:bg-gray-50 transition-colors">Rp 250k</button>
            <button className="py-3 rounded-xl border border-[#cfc6ac] text-[#181c1c] font-bold hover:bg-gray-50 transition-colors">Rp 500k</button>
          </div>

          <div className="flex flex-col gap-2 w-full">
            <label className="text-[#4c4733] text-sm font-bold">Nominal Donasi Lainnya</label>
            <div className="relative w-full">
              <div className="absolute left-4 top-1/2 -translate-y-1/2 text-[#4c4733] font-bold">Rp</div>
              <input 
                type="text" 
                placeholder="0" 
                className="w-full bg-[#f1f4f3] rounded-xl py-4 pl-12 pr-4 outline-none text-right font-bold text-gray-900 focus:ring-2 focus:ring-solar-gold"
              />
            </div>
          </div>

          <button className="w-full bg-solar-gold text-[#6d5b00] font-heading font-extrabold text-lg py-4 rounded-xl shadow-md hover:bg-yellow-500 transition-colors mt-2">
            Donasi Sekarang
          </button>

          <div className="flex items-center gap-4 py-4 border-t border-gray-100 mt-2">
            <div className="flex items-center -space-x-2">
              <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=60" alt="user" className="w-8 h-8 rounded-full border-2 border-white object-cover" />
              <img src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=100&auto=format&fit=crop&q=60" alt="user" className="w-8 h-8 rounded-full border-2 border-white object-cover" />
              <div className="w-8 h-8 rounded-full border-2 border-white bg-deep-navy text-white text-[10px] font-bold flex items-center justify-center">
                +24
              </div>
            </div>
            <p className="text-xs text-[#4c4733]">
              Bergabung dengan <span className="font-bold text-[#181c1c]">1,248 donatur</span> lainnya
            </p>
          </div>

          <div className="flex items-center gap-6 pt-2">
            <div className="flex items-center gap-2">
              <ShieldCheck className="w-4 h-4 text-green-600" />
              <span className="text-[10px] font-bold text-[#4c4733] uppercase">TRANSAKSI AMAN</span>
            </div>
            <div className="flex items-center gap-2">
              <Leaf className="w-4 h-4 text-green-600" />
              <span className="text-[10px] font-bold text-[#4c4733] uppercase">100% BERKELANJUTAN</span>
            </div>
          </div>

        </div>
      </div>

    </main>
  );
}
