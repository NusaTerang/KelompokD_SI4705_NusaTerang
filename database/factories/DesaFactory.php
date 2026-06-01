<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DesaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama_desa' => $this->faker->city,
            'kabupaten' => $this->faker->city,
            'provinsi' => $this->faker->state,
            'jumlah_penduduk' => $this->faker->numberBetween(100, 5000),
            'koordinat' => $this->faker->latitude . ',' . $this->faker->longitude,
            'sumber' => 'solar_panel',
        ];
    }
}
