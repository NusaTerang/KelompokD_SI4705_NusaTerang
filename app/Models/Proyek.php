<?php

namespace App\Models;

use App\Notifications\TargetDanaTercapai;
use App\Services\NotificationRecipientService;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Proyek extends Model
{
    use HasFactory;
    protected $fillable = [
        'desa_id', 'penyedia_id', 'judul', 'deskripsi', 'jenis_energi',
        'estimasi_mulai', 'estimasi_selesai', 'target_dana', 'dana_terkumpul', 'dana_terpakai',
        'expired_extension_pending', 'expired_original_end_date', 'expired_extended_at', 'expired_vendor_decision',
        'status', 'created_by', 'jadwal_publikasi'
    ];

    protected $casts = [
        'estimasi_mulai' => 'date',
        'estimasi_selesai' => 'date',
        'target_dana' => 'float',
        'dana_terkumpul' => 'float',
        'dana_terpakai' => 'float',
        'expired_extension_pending' => 'boolean',
        'expired_original_end_date' => 'date',
        'expired_extended_at' => 'datetime',
        'jadwal_publikasi' => 'datetime',
    ];

    /**
     * Apakah deadline (estimasi_selesai) sudah lewat?
     */
    public function isPastDeadline(): bool
    {
        return $this->estimasi_selesai !== null
            && $this->estimasi_selesai->endOfDay()->isPast();
    }

    /**
     * Proyek memenuhi syarat untuk refund donatur bila deadline sudah lewat
     * ATAU status resmi refund/dibatalkan.
     */
    public function isRefundEligible(): bool
    {
        return $this->isPastDeadline()
            || in_array($this->status, ['refund', 'dibatalkan'], true);
    }

    /**
     * Sisa dana yang belum terpakai (dasar refund). Tidak pernah negatif.
     */
    public function sisaDana(): float
    {
        return max(0, (float) $this->dana_terkumpul - (float) $this->dana_terpakai);
    }

    /**
     * Rasio dana yang dapat dikembalikan (0..1).
     * Contoh: terkumpul 100rb, terpakai 50rb -> rasio 0.5 (50% donasi bisa direfund).
     */
    public function refundRatio(): float
    {
        if ($this->dana_terkumpul <= 0) {
            return 0.0;
        }

        return min(1.0, max(0.0, $this->sisaDana() / (float) $this->dana_terkumpul));
    }

    /**
     * Besaran refund untuk satu nominal donasi berdasarkan rasio sisa dana.
     */
    public function refundAmountFor(float $nominal): float
    {
        return round($nominal * $this->refundRatio(), 2);
    }

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

    public function finalReports()
    {
        return $this->hasMany(LaporanAkhirProyekVendor::class, 'id_proyek');
    }

    public function submittedFinalReport()
    {
        return $this->hasOne(LaporanAkhirProyekVendor::class, 'id_proyek')
            ->where('status', 'submitted')
            ->latestOfMany('submitted_at');
    }

    public function donasis()
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

    public function checkAndActivateInstalasi()
    {
        if ($this->status === 'aktif_funding' && $this->dana_terkumpul >= $this->target_dana) {
            $this->update(['status' => 'eksekusi']);
            return true;
        }
        return false;
    }
}