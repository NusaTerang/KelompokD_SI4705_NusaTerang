<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Proyek;

class ProyekFotoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'proyek_id' => Proyek::factory(),
            'path' => 'proyek_fotos/sample.jpg',
            'urutan' => 1,
        ];
    }
}
