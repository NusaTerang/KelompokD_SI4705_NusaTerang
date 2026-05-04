import React from 'react';
import { MapPin, Mail, CheckCircle2, XCircle } from 'lucide-react';

export default function ProviderCard({ provider }) {
  const isActive = provider.status === 'AKTIF';

  return (
    <div className="w-full bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col gap-4 hover:shadow-md transition-shadow">
      
      {/* Top: Logo & Status */}
      <div className="flex justify-between items-start w-full">
        <div className="w-16 h-16 rounded-xl bg-gray-50 overflow-hidden shrink-0">
          <img 
            src={provider.logo} 
            alt={provider.name} 
            className="w-full h-full object-cover"
          />
        </div>
        
        <div className={`flex items-center gap-1.5 px-3 py-1 rounded-full ${isActive ? 'bg-green-50' : 'bg-gray-100'}`}>
          <div className={`w-1.5 h-1.5 rounded-full ${isActive ? 'bg-green-700' : 'bg-gray-500'}`}></div>
          <span className={`text-xs font-bold tracking-wider ${isActive ? 'text-green-700' : 'text-gray-500'}`}>
            {provider.status}
          </span>
        </div>
      </div>

      {/* Name */}
      <div className="w-full py-1">
        <h3 className="text-xl font-heading font-semibold text-gray-900 leading-tight">
          {provider.name}
        </h3>
      </div>

      {/* Contact Info */}
      <div className="flex flex-col gap-2 w-full">
        <div className="flex items-center gap-2 text-sm text-[#4c4733]">
          <MapPin className="w-4 h-4 shrink-0" />
          <span className="truncate">{provider.location}</span>
        </div>
        <div className="flex items-center gap-2 text-sm text-[#4c4733]">
          <Mail className="w-4 h-4 shrink-0" />
          <span className="truncate">{provider.email}</span>
        </div>
      </div>

      {/* Tags */}
      <div className="flex flex-wrap gap-2 w-full py-2">
        {provider.services.map((service, index) => (
          <div key={index} className="bg-[#ebeeed] px-3 py-1 rounded-lg">
            <span className="text-[#4c4733] text-xs font-semibold">{service}</span>
          </div>
        ))}
      </div>

      {/* Actions */}
      <div className="flex flex-col gap-3 w-full mt-auto pt-2">
        <button className="w-full py-3 rounded-xl border-2 border-deep-navy text-deep-navy font-bold hover:bg-gray-50 transition-colors">
          Lihat Profil
        </button>
        
        {isActive ? (
          <button className="w-full py-3 rounded-xl bg-solar-gold text-[#544600] font-bold flex items-center justify-center gap-2 hover:bg-yellow-500 transition-colors shadow-sm">
            <CheckCircle2 className="w-4 h-4" />
            Assign ke Proyek
          </button>
        ) : (
          <button className="w-full py-3 rounded-xl bg-[#ebeeed] text-[#7e7760] font-bold flex items-center justify-center gap-2 cursor-not-allowed">
            <XCircle className="w-4 h-4" />
            Tidak Tersedia
          </button>
        )}
      </div>

    </div>
  );
}
