namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenugasanProyek extends Model
{
    protected $table = 'penugasan_proyek';
    protected $primaryKey = 'id_penugasan';
    public $timestamps = false;

    protected $fillable = [
        'id_proyek','id_penyedia','status_penugasan'
    ];

    public function proyek()
    {
        return $this->belongsTo(Proyek::class, 'id_proyek');
    }

    public function detail()
    {
        return $this->hasOne(DetailProyekVendor::class, 'id_penugasan');
    }
}