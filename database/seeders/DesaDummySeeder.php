<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DesaDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['nama_desa' => 'Desa Cikaret', 'provinsi' => 'Jawa Barat', 'kabupaten' => 'Bogor',
<<<<<<< HEAD
             'koordinat' => '-6.5971, 106.8160', 'sumber' => 'solar_panel'],

            ['nama_desa' => 'Desa Sumber Makmur', 'provinsi' => 'Jawa Tengah', 'kabupaten' => 'Banjarnegara',
             'koordinat' => '-7.3606, 109.6946', 'sumber' => 'mikro_hidro'],

            ['nama_desa' => 'Desa Lembah Hijau', 'provinsi' => 'Sumatera Barat', 'kabupaten' => 'Solok',
             'koordinat' => '-0.7893, 100.6500', 'sumber' => 'solar_panel'],
=======
             'koordinat' => '-6.5971, 106.8160', 'sumber' => 'solar_panel',
             'status_verifikasi' => 'terverifikasi'],

            ['nama_desa' => 'Desa Sumber Makmur', 'provinsi' => 'Jawa Tengah', 'kabupaten' => 'Banjarnegara',
             'koordinat' => '-7.3606, 109.6946', 'sumber' => 'mikro_hidro',
             'status_verifikasi' => 'terverifikasi'],

            ['nama_desa' => 'Desa Lembah Hijau', 'provinsi' => 'Sumatera Barat', 'kabupaten' => 'Solok',
             'koordinat' => '-0.7893, 100.6500', 'sumber' => 'solar_panel',
             'status_verifikasi' => 'terverifikasi'],
>>>>>>> 9e75b2c7211ecd112192778de4861ab8d620a9fd
        ];

        foreach ($data as $desa) {
            \App\Models\Desa::create($desa);
        }
    }
}