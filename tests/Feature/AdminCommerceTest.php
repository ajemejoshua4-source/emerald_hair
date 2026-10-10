<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\DeliveryZone;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCommerceTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    public function test_admin_can_add_and_delete_a_delivery_zone(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.delivery-zones.store'), [
            'name' => 'Abuja',
            'fee' => 30,
        ])->assertRedirect();

        $zone = DeliveryZone::firstOrFail();
        $this->assertSame('Abuja', $zone->name);

        $this->actingAs($admin)->delete(route('admin.delivery-zones.delete', $zone))->assertRedirect();
        $this->assertDatabaseCount('delivery_zones', 0);
    }

    public function test_admin_can_manage_product_variants(): void
    {
        $admin = $this->admin();
        $product = Product::factory()->create();

        $this->actingAs($admin)->post(route('admin.variants.store', $product), [
            'label' => '22" / Natural',
            'sku' => 'WIG-22-NAT',
            'price' => 275,
            'stock' => 4,
            'is_active' => 1,
        ])->assertRedirect();

        $variant = ProductVariant::firstOrFail();
        $this->assertSame($product->id, $variant->product_id);

        $this->actingAs($admin)->put(route('admin.variants.update', $variant), [
            'label' => '22" / Natural',
            'sku' => 'WIG-22-NAT',
            'price' => 300,
            'stock' => 2,
            'is_active' => 1,
        ])->assertRedirect();

        $this->assertSame('300.00', $variant->fresh()->price);
        $this->assertSame(2, $variant->fresh()->stock);

        $this->actingAs($admin)->delete(route('admin.variants.delete', $variant))->assertRedirect();
        $this->assertDatabaseCount('product_variants', 0);
    }

    public function test_admin_can_save_store_settings(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->put(route('admin.settings.update'), [
            'store_email' => 'shop@emeraldhair.test',
            'store_phone' => '08000000000',
            'whatsapp' => '2348000000000',
            'instagram' => 'https://instagram.com/emeraldhair',
            'announcement' => 'Free delivery this week',
        ])->assertRedirect();

        $this->assertSame('shop@emeraldhair.test', Setting::get('store_email'));
        $this->assertSame('Free delivery this week', Setting::get('announcement'));
    }

    public function test_customers_cannot_manage_commerce_settings(): void
    {
        $customer = User::factory()->create(['is_admin' => false]);

        $this->actingAs($customer)->get(route('admin.delivery-zones'))->assertRedirect(route('login'));
        $this->actingAs($customer)->get(route('admin.settings'))->assertRedirect(route('login'));
    }

    public function test_admin_can_search_products(): void
    {
        $admin = $this->admin();
        $category = Category::factory()->create();
        Product::factory()->create(['name' => 'Burgundy Luxe Bob', 'category_id' => $category->id]);
        Product::factory()->create(['name' => 'Silky Straight', 'category_id' => $category->id]);

        $this->actingAs($admin)->get(route('admin.products', ['q' => 'Burgundy']))
            ->assertOk()
            ->assertSee('Burgundy Luxe Bob')
            ->assertDontSee('Silky Straight');
    }
}
