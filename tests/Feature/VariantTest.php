<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VariantTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_product_with_an_in_stock_variant_is_purchasable(): void
    {
        $product = Product::factory()->priceOnRequest()->create();
        ProductVariant::factory()->for($product)->create(['stock' => 3]);

        $this->assertTrue($product->fresh()->isPurchasable());
    }

    public function test_a_product_with_only_out_of_stock_variants_is_not_purchasable(): void
    {
        $product = Product::factory()->priceOnRequest()->create();
        ProductVariant::factory()->for($product)->outOfStock()->create();

        $this->assertFalse($product->fresh()->isPurchasable());
    }

    public function test_adding_a_variant_to_the_bag_records_the_variant(): void
    {
        $product = Product::factory()->priceOnRequest()->create();
        $variant = ProductVariant::factory()->for($product)->create([
            'label' => '20" / HD lace',
            'price' => 320,
            'stock' => 5,
        ]);

        $this->post(route('cart.add', $product), ['variant_id' => $variant->id, 'quantity' => 2])
            ->assertRedirect();

        $key = $product->id.':'.$variant->id;
        $this->assertSame('20" / HD lace', session('cart')[$key]['variant_label']);
        $this->assertSame(320.0, session('cart')[$key]['price']);
        $this->assertSame(2, session('cart')[$key]['quantity']);
    }

    public function test_checkout_uses_the_variant_price_and_decrements_variant_stock(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->priceOnRequest()->create();
        $variant = ProductVariant::factory()->for($product)->create(['price' => 200, 'stock' => 5]);

        $cart = [
            $product->id.':'.$variant->id => [
                'id' => $product->id,
                'variant_id' => $variant->id,
                'name' => $product->name,
                'variant_label' => $variant->label,
                'price' => 200.0,
                'quantity' => 2,
                'image' => null,
            ],
        ];

        $this->actingAs($user)
            ->withSession(['cart' => $cart])
            ->post(route('checkout.store'), [
                'customer_name' => 'Ada Buyer',
                'phone' => '08012345678',
            ])
            ->assertRedirect();

        $order = Order::firstOrFail();

        $this->assertSame('400.00', $order->total);
        $this->assertSame(3, $variant->fresh()->stock);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'variant_id' => $variant->id,
            'quantity' => 2,
        ]);
    }

    public function test_product_with_variants_requires_a_variant_selection(): void
    {
        $product = Product::factory()->priceOnRequest()->create();
        ProductVariant::factory()->for($product)->create(['stock' => 2]);

        $this->post(route('cart.add', $product), ['quantity' => 1])
            ->assertRedirect()
            ->assertSessionHas('error');
    }
}
