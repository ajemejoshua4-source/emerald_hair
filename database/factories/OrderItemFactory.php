<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 3);
        $unitPrice = fake()->randomFloat(2, 20, 500);

        return [
            'order_id' => Order::factory(),
            'product_id' => null,
            'product_name' => fake()->words(3, true),
            'unit_price' => $unitPrice,
            'quantity' => $quantity,
            'subtotal' => round($unitPrice * $quantity, 2),
        ];
    }
}
