<?php

namespace App\Http\Controllers;

use App\Models\Product;
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

    public function index()
    {
        return view('cart.index', ['cart' => $this->cart()]);
    }

    public function add(Request $request, Product $product)
    {
        $quantity = max(1, (int) $request->input('quantity', 1));

        if (! $product->isPurchasable()) {
            return back()->with('error', 'This piece is currently unavailable for online purchase. Please enquire on WhatsApp.');
        }

        $cart = $this->cart();
        $id = (string) $product->getKey();
        $existing = (int) ($cart[$id]['quantity'] ?? 0);

        $cart[$id] = [
            'id' => $product->getKey(),
            'name' => $product->name,
            'price' => (float) $product->price,
            'price_on_request' => false,
            'quantity' => min($product->stock, $existing + $quantity),
            'image' => $product->image,
        ];

        $this->saveCart($cart);

        return back()->with('success', 'Added to bag.');
    }

    public function update(Request $request)
    {
        $cart = $this->cart();

        foreach ((array) $request->input('quantity', []) as $id => $quantity) {
            $id = (string) $id;

            if (! isset($cart[$id])) {
                continue;
            }

            $quantity = max(0, (int) $quantity);

            if ($quantity === 0) {
                unset($cart[$id]);

                continue;
            }

            $cart[$id]['quantity'] = $quantity;
        }

        $this->saveCart($cart);

        return back()->with('success', 'Bag updated.');
    }

    public function remove(string $id)
    {
        $cart = $this->cart();
        unset($cart[$id]);

        $this->saveCart($cart);

        return back()->with('success', 'Item removed from bag.');
    }
}
