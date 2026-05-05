<?php

namespace Database\Seeders;

use App\Models\PenyediaEnergi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PenyediaDummySeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'penyedia' => [
                    'nama'              => 'SolarNusa Indonesia',
                    'spesialisasi'      => 'panel_surya',
                    'provinsi_operasi'  => 'Jawa Barat',
                    'kisaran_harga_min' => 100000000,
                    'kisaran_harga_max' => 200000000,
                    'rating'            => 4.8,
                    'status'            => 'aktif',
                    'email'             => 'solarnusa@penyedia.id',
                    'no_telepon'        => '081234560001',
                ],
                'user' => [
                    'nama'       => 'SolarNusa Indonesia',
                    'email'      => 'solarnusa@penyedia.id',
                    'password'   => Hash::make('password'),
                    'no_telepon' => '081234560001',
                    'role'       => 'penyedia',
                ],
            ],
            [
                'penyedia' => [
                    'nama'              => 'HidroTech Nusantara',
                    'spesialisasi'      => 'mikro_hidro',
                    'provinsi_operasi'  => 'Jawa Tengah',
                    'kisaran_harga_min' => 150000000,
                    'kisaran_harga_max' => 300000000,
                    'rating'            => 4.5,
                    'status'            => 'aktif',
                    'email'             => 'hidrotech@penyedia.id',
                    'no_telepon'        => '081234560002',
                ],
                'user' => [
                    'nama'       => 'HidroTech Nusantara',
                    'email'      => 'hidrotech@penyedia.id',
                    'password'   => Hash::make('password'),
                    'no_telepon' => '081234560002',
                    'role'       => 'penyedia',
                ],
            ],
            [
                'penyedia' => [
                    'nama'              => 'EnergiHijau Sumatera',
                    'spesialisasi'      => 'panel_surya',
                    'provinsi_operasi'  => 'Sumatera Barat',
                    'kisaran_harga_min' => 80000000,
                    'kisaran_harga_max' => 180000000,
                    'rating'            => 4.2,
                    'status'            => 'aktif',
                    'email'             => 'energihijau@penyedia.id',
                    'no_telepon'        => '081234560003',
                ],
                'user' => [
                    'nama'       => 'EnergiHijau Sumatera',
                    'email'      => 'energihijau@penyedia.id',
                    'password'   => Hash::make('password'),
                    'no_telepon' => '081234560003',
                    'role'       => 'penyedia',
                ],
            ],
            [
                'penyedia' => [
                    'nama'              => 'Surya Mandiri Group',
                    'spesialisasi'      => 'panel_surya',
                    'provinsi_operasi'  => 'Jawa Barat',
                    'kisaran_harga_min' => 90000000,
                    'kisaran_harga_max' => 160000000,
                    'rating'            => 4.0,
                    'status'            => 'aktif',
                    'email'             => 'suryamandiri@penyedia.id',
                    'no_telepon'        => '081234560004',
                ],
                'user' => [
                    'nama'       => 'Surya Mandiri Group',
                    'email'      => 'suryamandiri@penyedia.id',
                    'password'   => Hash::make('password'),
                    'no_telepon' => '081234560004',
                    'role'       => 'penyedia',
                ],
            ],
            [
                'penyedia' => [
                    'nama'              => 'PowerDesa Teknologi',
                    'spesialisasi'      => 'mikro_hidro',
                    'provinsi_operasi'  => 'Jawa Barat',
                    'kisaran_harga_min' => 120000000,
                    'kisaran_harga_max' => 250000000,
                    'rating'            => 3.9,
                    'status'            => 'aktif',
                    'email'             => 'powerdesa@penyedia.id',
                    'no_telepon'        => '081234560005',
                ],
                'user' => [
                    'nama'       => 'PowerDesa Teknologi',
                    'email'      => 'powerdesa@penyedia.id',
                    'password'   => Hash::make('password'),
                    'no_telepon' => '081234560005',
                    'role'       => 'penyedia',
                ],
            ],
        ];

        foreach ($data as $row) {
            $penyedia = PenyediaEnergi::create($row['penyedia']);

            User::create(array_merge($row['user'], [
                'penyedia_id' => $penyedia->id,
            ]));
        }
    }
}
