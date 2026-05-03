<?php
namespace Database\Seeders;
use App\Models\User;
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
        $this->call([
            DesaDummySeeder::class,
            PenyediaDummySeeder::class,
        ]);
    }
}
