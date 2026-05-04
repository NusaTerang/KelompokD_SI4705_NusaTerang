import React from 'react';

export default function CTASection() {
  return (
    <div className="w-full max-w-6xl mx-auto px-4 mb-24">
      <div className="bg-[#f1f4f3] rounded-[24px] p-12 flex flex-col md:flex-row items-center justify-between gap-12">
        
        <div className="flex flex-col gap-4 max-w-xl">
          <h2 className="text-3xl font-heading font-extrabold text-deep-navy leading-tight">
            Ingin proyek desa Anda dibantu?
          </h2>
          <p className="text-gray-600 text-base leading-relaxed">
            Daftarkan desa atau komunitas Anda sebagai penyedia energi dan dapatkan dukungan pendanaan dari donatur di seluruh dunia.
          </p>
        </div>

        <div className="flex flex-col sm:flex-row gap-4 w-full md:w-auto shrink-0">
          <button className="bg-deep-navy text-white font-heading font-bold px-8 py-4 rounded-xl hover:bg-blue-900 transition-colors shadow-lg">
            Ajukan Proyek
          </button>
          <button className="bg-white text-deep-navy border-2 border-deep-navy font-heading font-bold px-8 py-4 rounded-xl hover:bg-gray-50 transition-colors">
            Pelajari Lebih Lanjut
          </button>
        </div>

      </div>
    </div>
  );
}
