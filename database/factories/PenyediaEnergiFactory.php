<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PenyediaEnergiFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama' => $this->faker->company,
            'email' => $this->faker->companyEmail,
            'no_telepon' => $this->faker->phoneNumber,
            'spesialisasi' => 'panel_surya',
            'provinsi_operasi' => $this->faker->state,
            'kisaran_harga_min' => 10000000,
            'kisaran_harga_max' => 50000000,
            'rating' => 4.5,
            'status' => 'aktif',
        ];
    }
}
