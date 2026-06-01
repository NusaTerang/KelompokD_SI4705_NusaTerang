<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgressProyekVendor extends Model
{
    protected $table = 'progress_proyek_vendor';

    protected $primaryKey = 'id_progress';

    protected $fillable = [
        'id_penugasan',
        'persentase',
        'deskripsi',
        'foto_paths',
        'status_progress',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'foto_paths' => 'array',
        'submitted_at' => 'datetime',
    ];

    public function penugasan()
    {
        return $this->belongsTo(PenugasanProyek::class, 'id_penugasan', 'id_penugasan');
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }
}
