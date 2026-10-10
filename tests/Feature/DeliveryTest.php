<?php

namespace Tests\Feature;

use App\Models\DeliveryZone;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeliveryTest extends TestCase
{
    use RefreshDatabase;

    private function cart(Product $product, int $quantity = 1): array
    {
        return [
            (string) $product->id => [
                'id' => $product->id,
                'variant_id' => null,
                'name' => $product->name,
                'variant_label' => null,
                'price' => (float) $product->price,
                'quantity' => $quantity,
                'image' => null,
            ],
        ];
    }

    public function test_delivery_fee_is_added_to_the_order_total(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 100, 'stock' => 5]);
        $zone = DeliveryZone::factory()->create(['name' => 'Lagos', 'fee' => 25]);

        $this->actingAs($user)
            ->withSession(['cart' => $this->cart($product)])
            ->post(route('checkout.store'), [
                'customer_name' => 'Ada',
                'phone' => '08012345678',
                'delivery_zone_id' => $zone->id,
            ])
            ->assertRedirect();

        $order = Order::firstOrFail();

        $this->assertSame('100.00', $order->subtotal);
        $this->assertSame('25.00', $order->delivery_fee);
        $this->assertSame('125.00', $order->total);
        $this->assertSame($zone->id, $order->delivery_zone_id);
    }

    public function test_an_unknown_delivery_zone_is_rejected(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 100, 'stock' => 5]);

        $this->actingAs($user)
            ->withSession(['cart' => $this->cart($product)])
            ->post(route('checkout.store'), [
                'customer_name' => 'Ada',
                'phone' => '08012345678',
                'delivery_zone_id' => 9999,
            ])
            ->assertSessionHasErrors('delivery_zone_id');

        $this->assertDatabaseCount('orders', 0);
    }
}
