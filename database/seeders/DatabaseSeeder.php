<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\SaldoDonatur;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->updateOrCreate(
            ['email' => 'aditya.pratama@example.com'],
            [
                'nama' => 'Aditya Pratama',
                'password' => Hash::make('password'),
                'no_telepon' => '+62 812 3456 7890',
            ]
        );
        $user->forceFill(['created_at' => Carbon::parse('2023-01-15 08:00:00')])->saveQuietly();

        // Pastikan user memiliki saldo (update jika sudah ada)
        SaldoDonatur::updateOrCreate(
            ['id_donatur' => $user->id_donatur],
            ['saldo' => 1500000.00]
        );

        $this->call([
            AdminSeeder::class,
            DesaDummySeeder::class,
            PenyediaDummySeeder::class,
            ProyekDummySeeder::class,
            FakeDonationSeeder::class,
        ]);

        // Pastikan semua user yang dibuat oleh seeder lain punya saldo
        User::whereDoesntHave('saldoDonatur')->get()->each(function ($user) {
            SaldoDonatur::updateOrCreate(
                ['id_donatur' => $user->id_donatur],
                ['saldo' => 0.00]
            );
        });
    }
}