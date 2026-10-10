<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_from_admin(): void
    {
        $this->get('/admin')->assertRedirect(route('login'));
    }

    public function test_ordinary_customers_cannot_access_admin(): void
    {
        $customer = User::factory()->create(['is_admin' => false]);

        $this->actingAs($customer)
            ->get('/admin')
            ->assertRedirect(route('login'));
    }

    public function test_administrators_can_reach_the_dashboard(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->get('/admin')->assertOk();
    }

    public function test_administrators_can_create_a_product(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $category = Category::factory()->create();

        $this->actingAs($admin)->post(route('admin.products.store'), [
            'category_id' => $category->id,
            'name' => 'New Luxury Wig',
            'description' => 'A test product',
            'price' => 250,
            'stock' => 4,
            'is_active' => 1,
            'featured' => 1,
        ])->assertRedirect(route('admin.products'));

        $this->assertDatabaseHas('products', ['name' => 'New Luxury Wig', 'stock' => 4]);
    }

    public function test_customers_cannot_create_products(): void
    {
        $customer = User::factory()->create(['is_admin' => false]);
        $category = Category::factory()->create();

        $this->actingAs($customer)->post(route('admin.products.store'), [
            'category_id' => $category->id,
            'name' => 'Illegal Product',
            'stock' => 1,
        ])->assertRedirect(route('login'));

        $this->assertDatabaseMissing('products', ['name' => 'Illegal Product']);
    }

    public function test_administrators_can_update_order_status(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $order = Order::factory()->create();

        $this->actingAs($admin)->patch(route('admin.orders.update', $order), [
            'status' => 'shipped',
            'payment_status' => 'paid',
        ])->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'shipped',
            'payment_status' => 'paid',
        ]);
    }

    public function test_administrators_can_delete_a_product(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $product = Product::factory()->create();

        $this->actingAs($admin)
            ->delete(route('admin.products.delete', $product))
            ->assertRedirect();

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
