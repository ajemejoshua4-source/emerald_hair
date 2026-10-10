<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Administrator account. Password is overridable via env for deployment.
        $admin = User::firstOrNew(['email' => 'admin@emeraldhair.local']);
        $admin->name = 'Emerald Hair Admin';
        $admin->password = Hash::make(env('EMERALD_ADMIN_PASSWORD', 'ChangeMe123!'));
        $admin->is_admin = true;
        $admin->save();

        $categoryNames = [
            'Premium Wigs',
            'Hair Styling',
            'Luxury Bags',
            "Ladies' Heels",
            'Hair Gadgets',
            'Fabrics',
        ];

        $categories = collect($categoryNames)->mapWithKeys(function (string $name) {
            $category = Category::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'is_active' => true],
            );

            return [$name => $category];
        });

        // Brand products. Prices are intentionally left as "on request" until the
        // business supplies verified figures (see the development brief).
        $products = [
            ['Silky Straight Closure Wig', 'Premium Wigs', 'assets/straight-wig.jpg', 'A sleek, polished straight unit with a clean centre part and a soft, natural-looking finish.', 'Signature'],
            ['Classic Mocha Bob', 'Premium Wigs', 'assets/brown-bob-wig.jpg', 'A refined shoulder-length bob in a warm mocha brown tone for an effortless everyday look.', 'New'],
            ['Burgundy Luxe Bob', 'Premium Wigs', 'assets/burgundy-bob-wig.jpg', 'A rich burgundy bob with a smooth blunt finish for a bold, editorial statement.', 'Statement'],
        ];

        foreach ($products as [$name, $category, $image, $description, $badge]) {
            Product::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'category_id' => $categories[$category]->id,
                    'name' => $name,
                    'image' => $image,
                    'description' => $description,
                    'badge' => $badge,
                    'stock' => 10,
                    'price_on_request' => true,
                    'is_active' => true,
                    'featured' => true,
                ],
            );
        }
    }
}
