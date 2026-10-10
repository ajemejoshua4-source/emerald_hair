<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Get the cart contents from the session.
     *
     * @return array<string, array<string, mixed>>
     */
    private function cart(): array
    {
        return session('cart', []);
    }

    private function saveCart(array $cart): void
    {
        session(['cart' => $cart]);
    }

    private function cartKey(Product $product, ?ProductVariant $variant): string
    {
        return $variant ? $product->getKey().':'.$variant->getKey() : (string) $product->getKey();
    }

    public function index()
    {
        return view('cart.index', ['cart' => $this->cart()]);
    }

    public function add(Request $request, Product $product)
    {
        $quantity = max(1, (int) $request->input('quantity', 1));

        if (! $product->is_active) {
            return back()->with('error', 'This piece is currently unavailable.');
        }

        $variant = null;

        if ($product->hasVariants()) {
            $variant = $product->activeVariants()->find($request->integer('variant_id') ?: null);

            if (! $variant) {
                return back()->with('error', 'Please choose an available option for this piece.');
            }
        }

        $unitPrice = $variant
            ? $variant->unitPrice()
            : (! $product->price_on_request && $product->price !== null ? (float) $product->price : null);

        $stock = $variant ? $variant->stock : $product->stock;

        if ($unitPrice === null || $stock < 1) {
            return back()->with('error', 'This piece is currently unavailable for online purchase. Please enquire on WhatsApp.');
        }

        $cart = $this->cart();
        $key = $this->cartKey($product, $variant);
        $existing = (int) ($cart[$key]['quantity'] ?? 0);

        $cart[$key] = [
            'id' => $product->getKey(),
            'variant_id' => $variant?->getKey(),
            'name' => $product->name,
            'variant_label' => $variant?->label,
            'price' => (float) $unitPrice,
            'quantity' => min($stock, $existing + $quantity),
            'image' => $product->image,
        ];

        $this->saveCart($cart);

        return back()->with('success', 'Added to bag.');
    }

    public function update(Request $request)
    {
        $cart = $this->cart();

        foreach ((array) $request->input('quantity', []) as $key => $quantity) {
            $key = (string) $key;

            if (! isset($cart[$key])) {
                continue;
            }

            $quantity = max(0, (int) $quantity);

            if ($quantity === 0) {
                unset($cart[$key]);

                continue;
            }

            $cart[$key]['quantity'] = $quantity;
        }

        $this->saveCart($cart);

        return back()->with('success', 'Bag updated.');
    }

    public function remove(string $key)
    {
        $cart = $this->cart();
        unset($cart[$key]);

        $this->saveCart($cart);

        return back()->with('success', 'Item removed from bag.');
    }
}
