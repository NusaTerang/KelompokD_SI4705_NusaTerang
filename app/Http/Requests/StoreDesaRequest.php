<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDesaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_desa' => ['required', 'string', 'max:100'],
            'provinsi' => ['required', 'string', 'max:100'],
            'kabupaten' => ['required', 'string', 'max:100'],
            'kecamatan' => ['nullable', 'string', 'max:100'],
            'kode_wilayah' => ['nullable', 'string', 'max:50'],
            'koordinat' => ['nullable', 'string', 'max:100'],
            'kondisi_desa' => ['nullable', 'string'],
            'jumlah_penduduk' => ['nullable', 'integer', 'min:0'],
            'jumlah_kk' => ['nullable', 'integer', 'min:0'],
            'status_elektrifikasi' => ['nullable', Rule::in(['belum_teraliri', 'sebagian', 'sudah_teraliri'])],
            'estimasi_kebutuhan_daya' => ['nullable', 'string', 'max:50'],
            'catatan_tambahan' => ['nullable', 'string'],
            'sumber' => ['required', Rule::in(['solar_panel', 'mikro_hidro', 'biogas', 'hybrid'])],
            'action' => ['nullable', Rule::in(['draft', 'submit'])],
        ];
    }
}
