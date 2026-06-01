<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'        => User::factory(),
            'number'         => 'NT-' . strtoupper(Str::random(10)),
            'total_price'    => $this->faker->randomElement([25000, 50000, 100000, 150000, 200000]),
            'donatur_name'   => $this->faker->name(),
            'donatur_email'  => $this->faker->safeEmail(),
            'donatur_phone'  => '08' . $this->faker->numerify('#########'),
            'pesan'          => $this->faker->optional()->sentence(10),
            'payment_status' => Order::STATUS_PENDING,
            'snap_token'     => null,
        ];
    }
}
