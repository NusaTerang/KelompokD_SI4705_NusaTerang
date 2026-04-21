namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proyek extends Model
{
    protected $table = 'proyek';
    protected $primaryKey = 'id_proyek';
    public $timestamps = false;

    protected $fillable = [
        'id_desa','id_admin','judul','deskripsi',
        'target_dana','dana_terkumpul','status_proyek'
    ];

    public function penugasan()
    {
        return $this->hasMany(PenugasanProyek::class, 'id_proyek');
    }
}