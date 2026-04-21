namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PenugasanProyek;
use App\Models\DetailProyekVendor;

class PenugasanController extends Controller
{
    // Admin assign penyedia
    public function assign(Request $request)
    {
        $request->validate([
            'id_proyek' => 'required',
            'id_penyedia' => 'required'
        ]);

        $data = PenugasanProyek::create([
            'id_proyek' => $request->id_proyek,
            'id_penyedia' => $request->id_penyedia
        ]);

        return response()->json([
            'message' => 'Penugasan berhasil',
            'data' => $data
        ]);
    }

    // Vendor respon
    public function respon($id, Request $request)
    {
        $request->validate([
            'status' => 'required|in:diterima,ditolak'
        ]);

        $penugasan = PenugasanProyek::findOrFail($id);

        $penugasan->update([
            'status_penugasan' => $request->status,
            'tanggal_respon' => now()
        ]);

        return response()->json(['message' => 'Respon berhasil']);
    }

    / Vendor isi detail proyek
    public function isiDetail(Request $request)
    {
        $request->validate([
            'id_penugasan' => 'required',
            'spesifikasi' => 'required',
            'estimasi_biaya' => 'required',
            'durasi_pengerjaan' => 'required'
        ]);

        $detail = DetailProyekVendor::create($request->all());

        return response()->json([
            'message' => 'Detail tersimpan',
            'data' => $detail
        ]);
    }
}