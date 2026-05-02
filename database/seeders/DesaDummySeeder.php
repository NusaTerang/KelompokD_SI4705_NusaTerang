<?php

namespace Database\Seeders;

use App\Models\Desa;
use Illuminate\Database\Seeder;

class DesaDummySeeder extends Seeder
{
    public function run(): void
    {
        $desas = [
            ['nama' => 'Desa Cikaret', 'provinsi' => 'Jawa Barat', 'kabupaten' => 'Bogor',
             'lat' => -6.5971, 'lng' => 106.8160, 'jenis_energi' => 'solar',
             'estimasi_biaya' => 150000000, 'status' => 'terverifikasi'],

            ['nama' => 'Desa Sumber Makmur', 'provinsi' => 'Jawa Tengah', 'kabupaten' => 'Banjarnegara',
             'lat' => -7.3606, 'lng' => 109.6946, 'jenis_energi' => 'mikro_hidro',
             'estimasi_biaya' => 200000000, 'status' => 'terverifikasi'],

            ['nama' => 'Desa Lembah Hijau', 'provinsi' => 'Sumatera Barat', 'kabupaten' => 'Solok',
             'lat' => -0.7893, 'lng' => 100.6500, 'jenis_energi' => 'solar',
             'estimasi_biaya' => 120000000, 'status' => 'terverifikasi'],
        ];

        foreach ($desas as $desa) {
            Desa::create($desa);
        }
    }
}
