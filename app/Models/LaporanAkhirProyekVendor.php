<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanAkhirProyekVendor extends Model
{
    protected $table = 'laporan_akhir_proyek_vendor';

    protected $primaryKey = 'id_laporan';

    protected $fillable = [
        'id_penugasan',
        'id_proyek',
        'id_penyedia',
        'deskripsi',
        'kapasitas_terpasang',
        'satuan_kapasitas',
        'foto_paths',
        'catatan',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'kapasitas_terpasang' => 'decimal:2',
        'foto_paths' => 'array',
        'submitted_at' => 'datetime',
    ];

    public function penugasan()
    {
        return $this->belongsTo(PenugasanProyek::class, 'id_penugasan', 'id_penugasan');
    }

    public function proyek()
    {
        return $this->belongsTo(Proyek::class, 'id_proyek');
    }

    public function penyedia()
    {
        return $this->belongsTo(PenyediaEnergi::class, 'id_penyedia');
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }
}
