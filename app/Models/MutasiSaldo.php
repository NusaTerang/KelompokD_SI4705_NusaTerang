<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MutasiSaldo extends Model
{
    protected $table = 'mutasi_saldo';

    protected $fillable = [
        'id_donatur',
        'tipe',
        'nominal',
        'saldo_sebelum',
        'saldo_sesudah',
        'referensi_proyek_id',
        'keterangan',
    ];

    protected $casts = [
        'nominal'       => 'decimal:2',
        'saldo_sebelum' => 'decimal:2',
        'saldo_sesudah' => 'decimal:2',
    ];

    public function donatur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_donatur', 'id_donatur');
    }

    public function proyek(): BelongsTo
    {
        return $this->belongsTo(Proyek::class, 'referensi_proyek_id');
    }

    public function isCredit(): bool
    {
        return in_array($this->tipe, ['refund', 'topup']);
    }

    public function isDebit(): bool
    {
        return $this->tipe === 'donasi';
    }
}
