<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'title',
        'target_dana',
        'dana_terkumpul',
        'funding_status',
    ];

    protected $casts = [
        'target_dana' => 'float',
        'dana_terkumpul' => 'float',
    ];

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class, 'project_id');
    }
}
