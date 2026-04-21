<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proyek extends Model
{
    protected $guarded = [];

    protected $casts = [
        'estimasi_mulai' => 'date',
        'estimasi_selesai' => 'date',
    ];

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
