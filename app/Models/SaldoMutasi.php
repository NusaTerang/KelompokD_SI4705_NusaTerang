<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaldoMutasi extends Model
{
    protected $table = 'saldo_mutasi';

    protected $primaryKey = 'id_mutasi';

    protected $fillable = [
        'id_donatur',
        'tipe',
        'nominal',
        'saldo_sebelum',
        'saldo_sesudah',
        'keterangan',
        'proyek_id',
    ];

    protected $casts = [
        'nominal'       => 'float',
        'saldo_sebelum' => 'float',
        'saldo_sesudah' => 'float',
    ];

    public function donatur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_donatur', 'id_donatur');
    }

    public function proyek(): BelongsTo
    {
        return $this->belongsTo(Proyek::class, 'proyek_id');
    }

    public function isCredit(): bool
    {
        return $this->nominal >= 0;
    }
}
