<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaldoDonatur extends Model
{
    protected $table = 'saldo_donatur';

    protected $fillable = [
        'id_donatur',
        'saldo',
    ];

    protected $casts = [
        'saldo' => 'decimal:2',
    ];

    public function donatur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_donatur', 'id_donatur');
    }
}
