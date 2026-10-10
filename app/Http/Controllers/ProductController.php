<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('created_at', 'desc')->paginate(12);

        return view('products.index', compact('products'));
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();

        return view('products.show', compact('product'));
    }
}
