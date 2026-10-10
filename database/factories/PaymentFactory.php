<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'provider' => 'paystack',
            'reference' => 'EH-PAY-'.strtoupper(Str::random(10)),
            'amount' => fake()->randomFloat(2, 20, 900),
            'currency' => 'NGN',
            'status' => 'pending',
            'payload' => null,
        ];
    }

    public function successful(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'success']);
    }
}
