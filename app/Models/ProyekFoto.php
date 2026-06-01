<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProyekFoto extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function proyek()
    {
        return $this->belongsTo(Proyek::class);
    }
}
