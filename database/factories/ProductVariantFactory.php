<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'sku' => 'SKU-'.strtoupper(Str::random(8)),
            'label' => fake()->randomElement(['18"', '20"', '22"', '24"']).' / '.fake()->randomElement(['Natural', 'Burgundy', 'Jet Black']),
            'options' => ['length' => fake()->randomElement(['18"', '20"', '22"'])],
            'price' => fake()->randomFloat(2, 50, 600),
            'stock' => fake()->numberBetween(1, 20),
            'is_active' => true,
        ];
    }

    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => ['stock' => 0]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['is_active' => false]);
    }
}
