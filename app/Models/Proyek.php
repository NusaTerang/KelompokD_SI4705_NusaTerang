<?php

namespace App\Models;

use App\Notifications\TargetDanaTercapai;
use App\Services\NotificationRecipientService;
use Illuminate\Database\Eloquent\Model;

class Proyek extends Model
{
    protected $fillable = [
        'desa_id', 'penyedia_id', 'judul', 'deskripsi', 'jenis_energi',
        'estimasi_mulai', 'estimasi_selesai', 'target_dana', 'dana_terkumpul',
        'expired_extension_pending', 'expired_original_end_date', 'expired_extended_at', 'expired_vendor_decision',
        'status', 'created_by',
    ];

    protected $casts = [
        'estimasi_mulai' => 'date',
        'estimasi_selesai' => 'date',
        'expired_extension_pending' => 'boolean',
        'expired_original_end_date' => 'date',
        'expired_extended_at' => 'datetime',
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

    public function progressUpdates()
    {
        return $this->hasManyThrough(
            ProgressProyekVendor::class,
            PenugasanProyek::class,
            'id_proyek',
            'id_penugasan',
            'id',
            'id_penugasan'
        );
    }

    public function submittedProgressUpdates()
    {
        return $this->progressUpdates()
            ->where('progress_proyek_vendor.status', 'submitted')
            ->orderByDesc('progress_proyek_vendor.submitted_at');
    }

    public function donasi()
    {
        return $this->hasMany(Donasi::class, 'id_proyek');
    }

    public function recordFunding(float|int $amount): void
    {
        $wasBelowTarget = $this->dana_terkumpul < $this->target_dana;

        $this->increment('dana_terkumpul', $amount);
        $this->refresh();

        if ($wasBelowTarget && $this->dana_terkumpul >= $this->target_dana) {
            app(NotificationRecipientService::class)
                ->adminsAndDonorsForProject($this)
                ->each(fn ($recipient) => $recipient->notify(new TargetDanaTercapai($this)));
        }
    }
}