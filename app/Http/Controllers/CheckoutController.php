<?php

namespace App\Http\Controllers;

use App\Models\DeliveryZone;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
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

        return view('checkout.create', [
            'cart' => $cart,
            'zones' => DeliveryZone::active()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:150'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'delivery_zone_id' => ['nullable', 'integer', 'exists:delivery_zones,id'],
        ]);

        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('shop')->with('error', 'Your bag is empty.');
        }

        $zone = null;

        if (! empty($data['delivery_zone_id'])) {
            $zone = DeliveryZone::active()->find($data['delivery_zone_id']);

            if (! $zone) {
                return back()->with('error', 'Please choose a valid delivery area.')->withInput();
            }
        }

        try {
            $order = DB::transaction(function () use ($data, $cart, $zone) {
                $subtotal = 0.0;
                $items = [];

                foreach ($cart as $row) {
                    $product = Product::query()->lockForUpdate()->find($row['id'] ?? null);

                    if (! $product || ! $product->is_active) {
                        throw new \RuntimeException('A piece in your bag is no longer available.');
                    }

                    $variant = null;
                    $variantId = $row['variant_id'] ?? null;

                    if ($variantId) {
                        $variant = ProductVariant::query()->lockForUpdate()->find($variantId);

                        if (! $variant || $variant->product_id !== $product->getKey() || ! $variant->is_active) {
                            throw new \RuntimeException('A selected option is no longer available.');
                        }
                    } elseif ($product->hasVariants()) {
                        throw new \RuntimeException('Please reselect an option for '.$product->name.'.');
                    }

                    $quantity = (int) ($row['quantity'] ?? 0);

                    if ($quantity < 1) {
                        throw new \RuntimeException('Please choose a valid quantity for each piece.');
                    }

                    $unitPrice = $variant
                        ? $variant->unitPrice()
                        : (! $product->price_on_request && $product->price !== null ? (float) $product->price : null);

                    if ($unitPrice === null) {
                        throw new \RuntimeException('A piece in your bag is no longer available.');
                    }

                    $stock = $variant ? $variant->stock : $product->stock;

                    if ($stock < $quantity) {
                        throw new \RuntimeException('We only have '.$stock.' left of '.$product->name.'.');
                    }

                    $lineTotal = round($unitPrice * $quantity, 2);
                    $subtotal += $lineTotal;

                    $items[] = [
                        'product_id' => $product->getKey(),
                        'variant_id' => $variant?->getKey(),
                        'product_name' => $product->name,
                        'variant_label' => $variant?->label,
                        'unit_price' => $unitPrice,
                        'quantity' => $quantity,
                        'subtotal' => $lineTotal,
                    ];

                    if ($variant) {
                        $variant->decrement('stock', $quantity);
                    } else {
                        $product->decrement('stock', $quantity);
                    }
                }

                if (empty($items)) {
                    throw new \RuntimeException('Your bag is empty.');
                }

                $deliveryFee = $zone ? (float) $zone->fee : 0.0;

                $order = Order::create([
                    'user_id' => auth()->id(),
                    'order_number' => $this->generateOrderNumber(),
                    'customer_name' => $data['customer_name'],
                    'email' => $data['email'] ?? null,
                    'phone' => $data['phone'],
                    'address' => $data['address'] ?? null,
                    'notes' => $data['notes'] ?? null,
                    'subtotal' => round($subtotal, 2),
                    'delivery_fee' => $deliveryFee,
                    'currency' => 'NGN',
                    'delivery_zone_id' => $zone?->getKey(),
                    'total' => round($subtotal + $deliveryFee, 2),
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

        $order->load('items');

        $message = sprintf(
            "Hello Emerald Hair, I would like to confirm Order %s.\nCustomer: %s\nPhone: %s\nTotal: ₦%s",
            $order->order_number,
            $order->customer_name,
            $order->phone,
            number_format((float) $order->total, 2),
        );

        return view('checkout.success', [
            'order' => $order,
            'wa' => 'https://wa.me/'.config('emerald.whatsapp').'?text='.urlencode($message),
            'paystackEnabled' => (bool) config('paystack.secret'),
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
