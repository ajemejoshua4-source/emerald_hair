<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class StorefrontController extends Controller
{
    public function home()
    {
        $products = Product::with('category')
            ->where('is_active', true)
            ->where('featured', true)
            ->latest()
            ->take(6)
            ->get();

        $categories = Category::where('is_active', true)
            ->withCount('products')
            ->get();

        return view('home', compact('products', 'categories'));
    }

    public function shop(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $category = (string) $request->query('category', '');
        $sort = (string) $request->query('sort', 'latest');

        $products = Product::with('category')
            ->where('is_active', true)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('name', 'like', '%'.$q.'%')
                        ->orWhere('description', 'like', '%'.$q.'%');
                });
            })
            ->when($category !== '', function ($query) use ($category) {
                $query->whereHas('category', fn ($c) => $c->where('slug', $category));
            })
            ->when($sort === 'price_asc', fn ($query) => $query->orderByRaw('price IS NULL, price asc'))
            ->when($sort === 'price_desc', fn ($query) => $query->orderByRaw('price IS NULL, price desc'))
            ->when(! in_array($sort, ['price_asc', 'price_desc'], true), fn ($query) => $query->latest())
            ->paginate(12)
            ->withQueryString();

        $categories = Category::where('is_active', true)->get();

        return view('shop.index', compact('products', 'categories', 'q', 'category', 'sort'));
    }

    public function product(Product $product)
    {
        abort_unless($product->is_active, 404);

        $related = Product::with('category')
            ->where('is_active', true)
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->getKey())
            ->latest()
            ->take(3)
            ->get();

        return view('shop.show', compact('product', 'related'));
    }
}
