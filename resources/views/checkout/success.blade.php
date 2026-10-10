@extends('layouts.app')

@section('content')
<section class="section">
    <div class="wrap">
        <div class="eyebrow">Thank you</div>
        <h2>Order {{ $order->order_number }}</h2>

        <p>We have received your order, {{ $order->customer_name }}. Our team will confirm it with you shortly.</p>

        <table class="table" style="max-width:640px;margin-top:25px">
            <tr><th>Item</th><th>Qty</th><th>Subtotal</th></tr>
            @foreach($order->items as $item)
                <tr>
                    <td>
                        {{ $item->product_name }}
                        @if($item->variant_label)
                            <div class="muted">{{ $item->variant_label }}</div>
                        @endif
                    </td>
                    <td>{{ $item->quantity }}</td>
                    <td>₦{{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="2" style="text-align:right">Subtotal</td>
                <td>₦{{ number_format($order->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td colspan="2" style="text-align:right">Delivery</td>
                <td>₦{{ number_format($order->delivery_fee, 2) }}</td>
            </tr>
            <tr>
                <td colspan="2" style="text-align:right"><strong>Total</strong></td>
                <td><strong>₦{{ number_format($order->total, 2) }}</strong></td>
            </tr>
            <tr>
                <td colspan="2" style="text-align:right">Payment</td>
                <td>{{ $order->isPaid() ? 'Paid' : 'Unpaid' }}</td>
            </tr>
        </table>

        <div style="margin-top:25px;display:flex;gap:12px;flex-wrap:wrap">
            @if($paystackEnabled && ! $order->isPaid())
                <a class="btn" href="{{ route('payments.pay', $order) }}">Pay online now</a>
            @endif

            <a class="btn alt" target="_blank" rel="noopener" href="{{ $wa }}">Confirm on WhatsApp</a>
            <a class="btn alt" href="{{ route('shop') }}">Continue shopping</a>
        </div>

        <p class="muted" style="margin-top:20px;font-size:13px">
            Orders are only marked paid after payment is verified with our payment provider.
        </p>
    </div>
</section>
@endsection
