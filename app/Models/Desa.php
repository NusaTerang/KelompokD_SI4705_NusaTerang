<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Desa extends Model
{
    protected $guarded = [];

    public function proyeks()
    {
        return $this->hasMany(Proyek::class);
    }
}
