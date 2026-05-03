<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProyekFoto extends Model
{
    protected $guarded = [];

    public function proyek()
    {
        return $this->belongsTo(Proyek::class);
    }
}
