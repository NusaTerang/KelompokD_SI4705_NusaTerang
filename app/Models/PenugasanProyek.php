<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenugasanProyek extends Model
{
    protected $table = 'penugasan_proyek';
    protected $primaryKey = 'id_penugasan';
    protected $fillable = [
        'id_proyek', 'id_penyedia', 'status_penugasan', 'tanggal_respon',
    ];

    public function proyek()
    {
        return $this->belongsTo(Proyek::class, 'id_proyek');
    }

    public function detail()
    {
        return $this->hasOne(DetailProyekVendor::class, 'id_penugasan');
    }

    public function progressUpdates()
    {
        return $this->hasMany(ProgressProyekVendor::class, 'id_penugasan', 'id_penugasan');
    }

    public function submittedProgressUpdates()
    {
        return $this->hasMany(ProgressProyekVendor::class, 'id_penugasan', 'id_penugasan')
            ->where('status', 'submitted')
            ->orderByDesc('submitted_at');
    }

    public function progressDraft()
    {
        return $this->hasOne(ProgressProyekVendor::class, 'id_penugasan', 'id_penugasan')
            ->where('status', 'draft');
    }

    public function finalReport()
    {
        return $this->hasOne(LaporanAkhirProyekVendor::class, 'id_penugasan', 'id_penugasan');
    }

    public function finalReportDraft()
    {
        return $this->hasOne(LaporanAkhirProyekVendor::class, 'id_penugasan', 'id_penugasan')
            ->where('status', 'draft');
    }

    public function submittedFinalReport()
    {
        return $this->hasOne(LaporanAkhirProyekVendor::class, 'id_penugasan', 'id_penugasan')
            ->where('status', 'submitted');
    }

    public function hasCompletedProgress(): bool
    {
        return $this->submittedProgressUpdates
            ->contains(fn (ProgressProyekVendor $progress) => (int) $progress->persentase === 100 && $progress->status_progress === 'selesai');
    }
}