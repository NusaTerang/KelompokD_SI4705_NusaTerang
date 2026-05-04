import React from 'react';
import { Sun, Bell, User } from 'lucide-react';
import { Link, useLocation } from 'react-router-dom';

export default function AdminNavbar() {
  const location = useLocation();

  const getLinkClass = (path) => {
    const isActive = location.pathname === path;
    return isActive 
      ? "text-deep-navy font-bold border-b-2 border-solar-gold pb-1"
      : "text-[#4c4733] hover:text-deep-navy transition-colors";
  };

  return (
    <nav className="w-full h-[80px] bg-white border-b border-gray-200 flex items-center justify-between px-8 z-50 sticky top-0">
      <Link to="/" className="flex items-center gap-2 text-deep-navy font-heading font-extrabold text-xl">
        <Sun className="text-solar-gold fill-solar-gold w-6 h-6" />
        NusaTerang
      </Link>
      
      <div className="flex gap-8 text-sm font-medium">
        <Link to="/admin" className={getLinkClass('/admin')}>Dashboard</Link>
        <Link to="/admin/projects" className={getLinkClass('/admin/projects')}>Proyek Energi</Link>
        <Link to="/admin/villages" className={getLinkClass('/admin/villages')}>Data Desa</Link>
        <Link to="/providers" className={getLinkClass('/providers')}>Penyedia Energi</Link>
        <Link to="/admin/donations" className={getLinkClass('/admin/donations')}>Donasi</Link>
      </div>

      <div className="flex items-center gap-6">
        <button className="text-gray-500 hover:text-deep-navy transition-colors relative">
          <Bell className="w-5 h-5" />
          <span className="absolute -top-1 -right-1 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
        </button>
        <button className="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center border border-gray-300 text-gray-600 hover:bg-gray-200 transition-colors">
          <User className="w-4 h-4" />
        </button>
      </div>
    </nav>
  );
}
