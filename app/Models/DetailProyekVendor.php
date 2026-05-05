<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailProyekVendor extends Model
{
    protected $table = 'detail_proyek_vendor';
    protected $primaryKey = 'id_detail';
    protected $fillable = [
        'id_penugasan',
        'jenis_energi', 'kapasitas_daya', 'satuan_daya',
        'target_dana', 'cost_breakdown', 'durasi_minggu',
        'catatan_teknis', 'status',
        // legacy
        'spesifikasi', 'estimasi_biaya', 'durasi_pengerjaan', 'catatan',
    ];

    protected $casts = [
        'jenis_energi'   => 'array',
        'cost_breakdown' => 'array',
    ];
}