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
}