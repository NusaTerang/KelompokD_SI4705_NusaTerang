<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\SaldoDonatur;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@nusaterang.id'],
            [
                'nama' => 'Admin NusaTerang',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );

        SaldoDonatur::firstOrCreate(
            ['id_donatur' => $admin->id_donatur],
            ['saldo' => 0.00]
        );
    }
}
