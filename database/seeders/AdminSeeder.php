<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'nama' => 'Admin NusaTerang',
            'email' => 'admin@nusaterang.id',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
    }
}
