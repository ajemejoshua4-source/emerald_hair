<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    private function cartFor(Product $product, int $quantity): array
    {
        return [
            (string) $product->id => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => (float) $product->price,
                'price_on_request' => false,
                'quantity' => $quantity,
                'image' => $product->image,
            ],
        ];
    }

    public function test_guests_cannot_reach_checkout(): void
    {
        $this->get('/checkout')->assertRedirect(route('login'));
    }

    public function test_checkout_redirects_when_bag_is_empty(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/checkout')
            ->assertRedirect(route('shop'));
    }

    public function test_order_is_created_and_stock_decremented(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 100, 'stock' => 5]);

        $response = $this->actingAs($user)
            ->withSession(['cart' => $this->cartFor($product, 2)])
            ->post(route('checkout.store'), [
                'customer_name' => 'Ada Buyer',
                'email' => 'ada@example.com',
                'phone' => '08012345678',
                'address' => 'Kampala Road',
            ]);

        $order = Order::firstOrFail();

        $response->assertRedirect(route('checkout.success', $order));
        $this->assertSame('200.00', $order->total);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'subtotal' => 200,
        ]);
        $this->assertSame(3, $product->fresh()->stock);
        $this->assertNull(session('cart'));
    }

    public function test_orders_cannot_oversell_stock(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 50, 'stock' => 3]);

        $this->actingAs($user)
            ->from(route('checkout'))
            ->withSession(['cart' => $this->cartFor($product, 10)])
            ->post(route('checkout.store'), [
                'customer_name' => 'Ada Buyer',
                'phone' => '08012345678',
            ])
            ->assertRedirect(route('checkout'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(3, $product->fresh()->stock);
    }

    public function test_customers_cannot_view_another_customers_order(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $order = Order::factory()->for($owner)->create();

        $this->actingAs($other)
            ->get(route('checkout.success', $order))
            ->assertForbidden();
    }
}
