@extends('layouts.app')

@section('content')
<section class="section">
    <div class="wrap" style="display:grid;grid-template-columns:1.2fr .8fr;gap:45px">
        <div>
            <div class="eyebrow">Checkout</div>
            <h2>Complete your order</h2>

            <form class="form" method="post" action="{{ route('checkout.store') }}">
                @csrf

                <div class="field">
                    <label>Full name</label>
                    <input name="customer_name" value="{{ old('customer_name', auth()->user()->name) }}" required>
                </div>

                <div class="field">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}">
                </div>

                <div class="field">
                    <label>Phone / WhatsApp</label>
                    <input name="phone" value="{{ old('phone', auth()->user()->phone) }}" required>
                </div>

                <div class="field">
                    <label>Delivery address</label>
                    <textarea name="address" rows="3">{{ old('address') }}</textarea>
                </div>

                @if($zones->isNotEmpty())
                    <div class="field">
                        <label>Delivery area</label>
                        <select name="delivery_zone_id">
                            <option value="">Select a delivery area</option>
                            @foreach($zones as $zone)
                                <option value="{{ $zone->id }}" @selected(old('delivery_zone_id') == $zone->id)>
                                    {{ $zone->name }} — ₦{{ number_format($zone->fee, 2) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="field">
                    <label>Order notes (optional)</label>
                    <textarea name="notes" rows="2">{{ old('notes') }}</textarea>
                </div>

                <button class="btn">Place order</button>
            </form>
        </div>

        <aside>
            <h3>Order summary</h3>
            @php $subtotal = 0; @endphp
            <table class="table">
                @foreach($cart as $key => $row)
                    @php
                        $line = (float) $row['price'] * (int) $row['quantity'];
                        $subtotal += $line;
                    @endphp
                    <tr>
                        <td>
                            {{ $row['name'] }}
                            @if(! empty($row['variant_label']))
                                <div class="muted">{{ $row['variant_label'] }}</div>
                            @endif
                            <div class="muted">Qty {{ $row['quantity'] }}</div>
                        </td>
                        <td>₦{{ number_format($line, 2) }}</td>
                    </tr>
                @endforeach
            </table>
            <div style="margin-top:15px;font-size:18px">Subtotal: <strong>₦{{ number_format($subtotal, 2) }}</strong></div>
            <p class="muted" style="font-size:13px">Delivery is added based on your selected area. Orders are confirmed by WhatsApp.</p>
        </aside>
    </div>
</section>
@endsection
