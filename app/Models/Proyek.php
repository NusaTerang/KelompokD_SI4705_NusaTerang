<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proyek extends Model
{
    protected $table = 'proyek';
    protected $primaryKey = 'id_proyek';
    public $timestamps = false;

    protected $fillable = [
        'id_desa', 'id_admin', 'judul', 'deskripsi',
        'target_dana', 'dana_terkumpul', 'status_proyek',
        'estimasi_mulai', 'estimasi_selesai', 'penyedia_id', 'created_by',
    ];

    protected $casts = [
        'estimasi_mulai' => 'date',
        'estimasi_selesai' => 'date',
    ];

    public function penugasan()
    {
        return $this->hasMany(PenugasanProyek::class, 'id_proyek');
    }

    public function desa()
    {
        return $this->belongsTo(Desa::class);
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
}