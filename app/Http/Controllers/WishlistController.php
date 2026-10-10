<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;

class WishlistController extends Controller
{
    public function index()
    {
        return view('account.wishlist', [
            'items' => auth()->user()->wishlist()->with('product')->latest()->get(),
        ]);
    }

    public function toggle(Product $product)
    {
        $existing = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $product->getKey())
            ->first();

        if ($existing) {
            $existing->delete();

            return back()->with('success', 'Removed from wishlist.');
        }

        Wishlist::create([
            'user_id' => auth()->id(),
            'product_id' => $product->getKey(),
        ]);

        return back()->with('success', 'Saved to wishlist.');
    }
}
