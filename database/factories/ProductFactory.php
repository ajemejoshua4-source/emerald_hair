<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'category_id' => Category::factory(),
            'name' => Str::title($name),
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1, 99999),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 20, 500),
            'price_on_request' => false,
            'stock' => fake()->numberBetween(1, 25),
            'image' => 'assets/straight-wig.jpg',
            'badge' => null,
            'is_active' => true,
            'featured' => fake()->boolean(40),
        ];
    }

    public function priceOnRequest(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => null,
            'price_on_request' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 0,
        ]);
    }
}
