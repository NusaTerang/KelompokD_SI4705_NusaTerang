<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proyek extends Model
{
    protected $fillable = [
        'desa_id', 'penyedia_id', 'judul', 'deskripsi', 'jenis_energi',
        'estimasi_mulai', 'estimasi_selesai', 'target_dana', 'dana_terkumpul',
        'expired_extension_pending', 'expired_original_end_date', 'expired_extended_at', 'expired_vendor_decision',
        'status', 'created_by', 'jadwal_publikasi'
    ];

    protected $casts = [
        'estimasi_mulai' => 'date',
        'estimasi_selesai' => 'date',
        'expired_extension_pending' => 'boolean',
        'expired_original_end_date' => 'date',
        'expired_extended_at' => 'datetime',
        'jadwal_publikasi' => 'datetime',
    ];

    public function penugasan()
    {
        return $this->hasMany(PenugasanProyek::class, 'id_proyek'); // Assuming PenugasanProyek still uses id_proyek
    }

    public function desa()
    {
        return $this->belongsTo(Desa::class, 'desa_id', 'id_desa');
    }

    public function penyedia()
    {
        return $this->belongsTo(PenyediaEnergi::class, 'penyedia_id');
    }

    public function fotos()
    {
        return $this->hasMany(ProyekFoto::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function checkAndActivateInstalasi()
    {
        if ($this->status === 'aktif_funding' && $this->dana_terkumpul >= $this->target_dana) {
            $this->update(['status' => 'eksekusi']);
            return true;
        }
        return false;
    }
}