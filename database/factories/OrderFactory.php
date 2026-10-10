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
            'user_id' => User::factory(),
            'order_number' => 'EH-'.now()->format('Ymd').'-'.strtoupper(Str::random(6)),
            'customer_name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->numerify('080########'),
            'address' => fake()->address(),
            'notes' => null,
            'total' => 0,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ];
    }
}
