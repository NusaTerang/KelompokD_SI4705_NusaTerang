<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class PenyediaEnergi extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function proyeks()
    {
        return $this->hasMany(Proyek::class, 'penyedia_id');
    }
}
