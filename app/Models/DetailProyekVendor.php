namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailProyekVendor extends Model
{
    protected $table = 'detail_proyek_vendor';
    protected $primaryKey = 'id_detail';
    public $timestamps = false;

    protected $fillable = [
        'id_penugasan','spesifikasi','estimasi_biaya',
        'durasi_pengerjaan','catatan'
    ];
}