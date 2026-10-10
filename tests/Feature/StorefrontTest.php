<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_loads(): void
    {
        $this->get('/')->assertOk()->assertSee('Emerald Hair');
    }

    public function test_shop_lists_active_products_only(): void
    {
        $active = Product::factory()->create(['name' => 'Visible Wig']);
        Product::factory()->inactive()->create(['name' => 'Hidden Wig']);

        $this->get('/shop')
            ->assertOk()
            ->assertSee('Visible Wig')
            ->assertDontSee('Hidden Wig');
    }

    public function test_shop_search_filters_by_name(): void
    {
        Product::factory()->create(['name' => 'Burgundy Luxe Bob']);
        Product::factory()->create(['name' => 'Silky Straight Closure']);

        $this->get('/shop?q=Burgundy')
            ->assertOk()
            ->assertSee('Burgundy Luxe Bob')
            ->assertDontSee('Silky Straight Closure');
    }

    public function test_shop_filters_by_category(): void
    {
        $wigs = Category::factory()->create(['name' => 'Premium Wigs', 'slug' => 'premium-wigs']);
        $bags = Category::factory()->create(['name' => 'Luxury Bags', 'slug' => 'luxury-bags']);

        Product::factory()->create(['name' => 'Lace Frontal', 'category_id' => $wigs->id]);
        Product::factory()->create(['name' => 'Tote Bag', 'category_id' => $bags->id]);

        $this->get('/shop?category=premium-wigs')
            ->assertOk()
            ->assertSee('Lace Frontal')
            ->assertDontSee('Tote Bag');
    }

    public function test_product_detail_loads(): void
    {
        $product = Product::factory()->create(['name' => 'Classic Mocha Bob']);

        $this->get(route('products.show', $product))
            ->assertOk()
            ->assertSee('Classic Mocha Bob');
    }

    public function test_inactive_product_returns_404(): void
    {
        $product = Product::factory()->inactive()->create();

        $this->get(route('products.show', $product))->assertNotFound();
    }

    public function test_price_on_request_products_show_enquiry_label(): void
    {
        $product = Product::factory()->priceOnRequest()->create(['name' => 'On Request Wig']);

        $this->get(route('products.show', $product))
            ->assertOk()
            ->assertSee('Price on request');
    }
}
