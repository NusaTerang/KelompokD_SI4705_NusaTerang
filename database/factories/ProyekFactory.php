<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Desa;
use App\Models\User;

class ProyekFactory extends Factory
{
    public function definition(): array
    {
        return [
            'desa_id' => Desa::factory(),
            'judul' => $this->faker->sentence,
            'deskripsi' => $this->faker->paragraph,
            'jenis_energi' => 'panel_surya',
            'estimasi_mulai' => now(),
            'estimasi_selesai' => now()->addDays(30),
            'target_dana' => 100000000,
            'dana_terkumpul' => 0,
            'status' => 'draft',
            'created_by' => User::factory(),
        ];
    }
}
