<?php

namespace Database\Seeders;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MidtransSeeder extends Seeder
{
    use HasFactory;
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        \App\Models\Order::factory(10)->create();
    }
}
