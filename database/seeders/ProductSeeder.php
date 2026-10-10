<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run()
    {
        Product::truncate();

        $products = [
            ['name' => 'Silk Straight Extensions - 20"', 'slug' => 'silk-straight-20', 'description' => 'Premium silk-straight 100% human hair extensions.', 'price' => 149.00, 'image' => '/public/emerald.jpg'],
            ['name' => 'Luxury Lace Front Wig - Natural', 'slug' => 'luxury-lace-front', 'description' => 'Hand-tied lace front wig for natural hairline.', 'price' => 349.00, 'image' => '/public/emerald.jpg'],
            ['name' => 'Voluminous Bundles (3-pack)', 'slug' => 'voluminous-bundles', 'description' => 'Three bundles for full coverage and volume.', 'price' => 219.00, 'image' => '/public/emerald.jpg'],
        ];

        foreach ($products as $p) {
            Product::create($p);
        }
    }
}
