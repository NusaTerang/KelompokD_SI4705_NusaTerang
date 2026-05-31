<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donasi extends Model
{
    protected $table = 'donasi';

    protected $primaryKey = 'id_donasi';

    protected $fillable = [
        'id_donatur',
        'id_proyek',
        'nominal',
        'status',
    ];

    public function donatur()
    {
        return $this->belongsTo(User::class, 'id_donatur', 'id_donatur');
    }

    public function proyek()
    {
        return $this->belongsTo(Proyek::class, 'id_proyek');
    }
}
