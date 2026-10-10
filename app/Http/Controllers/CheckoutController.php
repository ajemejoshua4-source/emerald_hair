<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function create()
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('shop')->with('error', 'Your bag is empty.');
        }

        return view('checkout.create', ['cart' => $cart]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:150'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('shop')->with('error', 'Your bag is empty.');
        }

        try {
            $order = DB::transaction(function () use ($data, $cart) {
                $total = 0.0;
                $items = [];

                foreach ($cart as $row) {
                    $product = Product::query()->lockForUpdate()->find($row['id'] ?? null);

                    if (! $product || ! $product->isPurchasable()) {
                        throw new \RuntimeException('A piece in your bag is no longer available.');
                    }

                    $quantity = (int) ($row['quantity'] ?? 0);

                    if ($quantity < 1) {
                        throw new \RuntimeException('Please choose a valid quantity for each piece.');
                    }

                    if ($product->stock < $quantity) {
                        throw new \RuntimeException('We only have '.$product->stock.' left of '.$product->name.'.');
                    }

                    $unitPrice = (float) $product->price;
                    $subtotal = round($unitPrice * $quantity, 2);
                    $total += $subtotal;

                    $items[] = [
                        'product_id' => $product->getKey(),
                        'product_name' => $product->name,
                        'unit_price' => $unitPrice,
                        'quantity' => $quantity,
                        'subtotal' => $subtotal,
                    ];

                    $product->decrement('stock', $quantity);
                }

                if (empty($items)) {
                    throw new \RuntimeException('Your bag is empty.');
                }

                $order = Order::create([
                    'user_id' => auth()->id(),
                    'order_number' => $this->generateOrderNumber(),
                    'customer_name' => $data['customer_name'],
                    'email' => $data['email'] ?? null,
                    'phone' => $data['phone'],
                    'address' => $data['address'] ?? null,
                    'notes' => $data['notes'] ?? null,
                    'total' => round($total, 2),
                    'status' => 'pending',
                    'payment_status' => 'unpaid',
                ]);

                $order->items()->createMany($items);

                return $order;
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        session()->forget('cart');

        return redirect()->route('checkout.success', $order);
    }

    public function success(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        $message = sprintf(
            "Hello Emerald Hair, I would like to confirm Order %s.\nCustomer: %s\nPhone: %s\nTotal: ₦%s",
            $order->order_number,
            $order->customer_name,
            $order->phone,
            number_format((float) $order->total, 2),
        );

        $whatsapp = 'https://wa.me/'.config('emerald.whatsapp').'?text='.urlencode($message);

        return view('checkout.success', [
            'order' => $order->load('items'),
            'wa' => $whatsapp,
        ]);
    }

    private function generateOrderNumber(): string
    {
        do {
            $number = 'EH-'.now()->format('Ymd').'-'.strtoupper(Str::random(8));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }
}
