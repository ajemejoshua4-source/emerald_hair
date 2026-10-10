<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        $featuredCollections = [
            ['title' => 'Silk Extensions', 'slug' => 'silk-extensions', 'summary' => 'Soft, glossy extensions for a seamless blend.'],
            ['title' => 'Luxury Wigs', 'slug' => 'luxury-wigs', 'summary' => 'Handcrafted wigs with premium hair and natural caps.'],
            ['title' => 'Hair Bundles', 'slug' => 'hair-bundles', 'summary' => 'Full-volume bundles in curated lengths and textures.'],
        ];

        $testimonials = [
            ['name' => 'Olivia R.', 'quote' => 'The extensions looked and felt so natural — I felt glamorous!'],
            ['name' => 'Maya K.', 'quote' => 'Customer service was exceptional and delivery was fast.'],
        ];

        return view('home', compact('featuredCollections', 'testimonials'));
    }
}
