<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proyek extends Model
{
    protected $fillable = [
        'desa_id', 'penyedia_id', 'judul', 'deskripsi', 'jenis_energi',
        'estimasi_mulai', 'estimasi_selesai', 'target_dana', 'dana_terkumpul', 
        'status', 'created_by',
    ];

    protected $casts = [
        'estimasi_mulai' => 'date',
        'estimasi_selesai' => 'date',
    ];

    public function penugasan()
    {
        return $this->hasMany(PenugasanProyek::class, 'id_proyek'); // Assuming PenugasanProyek still uses id_proyek
    }

    public function desa()
    {
        return $this->belongsTo(Desa::class, 'desa_id', 'id_desa');
    }

    public function penyedia()
    {
        return $this->belongsTo(PenyediaEnergi::class, 'penyedia_id');
    }

    public function fotos()
    {
        return $this->hasMany(ProyekFoto::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function donasis()
    {
        return $this->hasMany(Donasi::class, 'id_proyek');
    }
}