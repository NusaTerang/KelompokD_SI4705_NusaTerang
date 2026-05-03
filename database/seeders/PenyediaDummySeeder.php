<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PenyediaDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['nama' => 'SolarNusa Indonesia', 'spesialisasi' => 'solar', 
             'provinsi_operasi' => 'Jawa Barat', 'kisaran_harga_min' => 100000000, 
             'kisaran_harga_max' => 200000000, 'rating' => 4.8, 'status' => 'aktif'],
          
            ['nama' => 'HidroTech Nusantara', 'spesialisasi' => 'mikro_hidro', 
             'provinsi_operasi' => 'Jawa Tengah', 'kisaran_harga_min' => 150000000, 
             'kisaran_harga_max' => 300000000, 'rating' => 4.5, 'status' => 'aktif'],
          
            ['nama' => 'EnergiHijau Sumatera', 'spesialisasi' => 'solar', 
             'provinsi_operasi' => 'Sumatera Barat', 'kisaran_harga_min' => 80000000, 
             'kisaran_harga_max' => 180000000, 'rating' => 4.2, 'status' => 'aktif'],
          
            ['nama' => 'Surya Mandiri Group', 'spesialisasi' => 'solar', 
             'provinsi_operasi' => 'Jawa Barat', 'kisaran_harga_min' => 90000000, 
             'kisaran_harga_max' => 160000000, 'rating' => 4.0, 'status' => 'aktif'],
          
            ['nama' => 'PowerDesa Teknologi', 'spesialisasi' => 'mikro_hidro', 
             'provinsi_operasi' => 'Jawa Barat', 'kisaran_harga_min' => 120000000, 
             'kisaran_harga_max' => 250000000, 'rating' => 3.9, 'status' => 'aktif'],
        ];

        foreach ($data as $penyedia) {
            \App\Models\PenyediaEnergi::create($penyedia);
        }
    }
}
