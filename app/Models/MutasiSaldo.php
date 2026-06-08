<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MutasiSaldo extends Model
{
    protected $table = 'mutasi_saldo';

    protected $fillable = [
        'id_donatur',
        'nominal',
        'tipe',
        'keterangan',
    ];

    protected $casts = [
        'nominal' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_donatur', 'id_donatur');
    }
}
