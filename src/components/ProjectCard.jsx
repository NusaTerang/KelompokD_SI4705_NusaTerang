import React from 'react';
import { MapPin, ArrowRight } from 'lucide-react';
import { Link } from 'react-router-dom';

export default function ProjectCard({ project }) {
  return (
    <div className="w-full bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow flex flex-col group">
      
      {/* Image & Badge */}
      <div className="relative h-48 w-full overflow-hidden">
        <img 
          src={project.image} 
          alt={project.title} 
          className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
        />
        <div className="absolute top-4 left-4 bg-white/90 backdrop-blur-sm shadow-sm rounded-full px-3 py-1.5 flex items-center gap-1.5">
          <MapPin className="w-3 h-3 text-deep-navy" />
          <span className="text-[10px] font-bold text-deep-navy tracking-wider uppercase">
            {project.locationBadge}
          </span>
        </div>
      </div>

      {/* Content */}
      <div className="p-6 flex flex-col flex-grow">
        
        <h3 className="text-xl font-heading font-bold text-gray-900 mb-2 leading-tight">
          {project.title}
        </h3>
        <p className="text-sm text-gray-600 leading-relaxed mb-6 flex-grow line-clamp-2">
          {project.description}
        </p>

        {/* Progress */}
        <div className="mb-6">
          <div className="flex justify-between items-end mb-2">
            <span className="text-2xl font-bold text-deep-navy leading-none">{project.progress}%</span>
            <span className="text-xs font-medium text-gray-600">Target: {project.targetText}</span>
          </div>
          <div className="w-full h-2.5 bg-gray-100 rounded-full overflow-hidden">
            <div 
              className="h-full bg-solar-gold rounded-full transition-all duration-1000 ease-out"
              style={{ width: `${project.progress}%` }}
            ></div>
          </div>
        </div>

        {/* Stats */}
        <div className="flex justify-between items-start border-t border-gray-100 pt-4 mb-4">
          <div>
            <p className="text-[10px] font-bold text-gray-500 tracking-wider uppercase mb-1">TERKUMPUL</p>
            <p className="text-sm font-bold text-gray-900">{project.raisedText}</p>
          </div>
          <div className="text-right">
            <p className="text-[10px] font-bold text-gray-500 tracking-wider uppercase mb-1">SISA HARI</p>
            <p className="text-sm font-bold text-gray-900">{project.daysLeft} Hari</p>
          </div>
        </div>

        {/* Action */}
        <Link to={`/project/${project.id}`} className="w-full py-3 rounded-lg border-2 border-deep-navy text-deep-navy font-heading font-bold text-sm flex items-center justify-center gap-2 hover:bg-deep-navy hover:text-white transition-colors mt-auto">
          Lihat Detail
          <ArrowRight className="w-4 h-4" />
        </Link>

      </div>
    </div>
  );
}
