<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Desa extends Model
{
    protected $table = 'desa';
    protected $primaryKey = 'id_desa';
    public const UPDATED_AT = null;
    protected $fillable = [
        'nama_desa',
        'provinsi',
        'kabupaten',
        'koordinat',
        'kondisi_desa',
        'sumber',
        'status_verifikasi',
        'id_admin',
    ];
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_admin');
    }
    public function proyeks()
    {
        return $this->hasMany(Proyek::class);
    }
}