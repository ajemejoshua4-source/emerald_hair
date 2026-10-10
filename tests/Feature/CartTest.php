<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_purchasable_product_can_be_added_to_the_bag(): void
    {
        $product = Product::factory()->create(['stock' => 5, 'price' => 120]);

        $this->post(route('cart.add', $product), ['quantity' => 2])
            ->assertRedirect();

        $this->assertSame(2, session('cart')[$product->id]['quantity']);
    }

    public function test_quantity_cannot_exceed_stock(): void
    {
        $product = Product::factory()->create(['stock' => 3]);

        $this->post(route('cart.add', $product), ['quantity' => 10]);

        $this->assertSame(3, session('cart')[$product->id]['quantity']);
    }

    public function test_price_on_request_product_cannot_be_added(): void
    {
        $product = Product::factory()->priceOnRequest()->create();

        $this->from(route('products.show', $product))
            ->post(route('cart.add', $product), ['quantity' => 1])
            ->assertRedirect(route('products.show', $product))
            ->assertSessionHas('error');

        $this->assertNull(session('cart'));
    }

    public function test_bag_update_and_remove(): void
    {
        $product = Product::factory()->create(['stock' => 10]);

        $this->post(route('cart.add', $product), ['quantity' => 1]);

        $this->patch(route('cart.update'), ['quantity' => [$product->id => 4]]);
        $this->assertSame(4, session('cart')[$product->id]['quantity']);

        $this->delete(route('cart.remove', $product->id));
        $this->assertEmpty(session('cart'));
    }
}
