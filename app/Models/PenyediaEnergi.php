<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenyediaEnergi extends Model
{
    protected $guarded = [];

    public function proyeks()
    {
        return $this->hasMany(Proyek::class, 'penyedia_id');
    }
}
