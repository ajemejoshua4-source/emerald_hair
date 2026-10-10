@extends('layouts.app')

@section('content')
<section class="section">
    <div class="wrap">
        <div class="eyebrow">Your selection</div>
        <h2>Shopping bag</h2>

        @if(empty($cart))
            <p>Your bag is empty. <a style="color:var(--green)" href="{{ route('shop') }}">Continue shopping.</a></p>
        @else
            @php $total = 0; @endphp

            <form method="post" action="{{ route('cart.update') }}">
                @csrf
                @method('PATCH')

                <table class="table">
                    <tr>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th></th>
                    </tr>

                    @foreach($cart as $key => $row)
                        @php
                            $line = (float) $row['price'] * (int) $row['quantity'];
                            $total += $line;
                        @endphp
                        <tr>
                            <td>
                                {{ $row['name'] }}
                                @if(! empty($row['variant_label']))
                                    <div class="muted">{{ $row['variant_label'] }}</div>
                                @endif
                            </td>
                            <td>
                                <input style="width:70px;padding:8px" type="number"
                                       name="quantity[{{ $key }}]"
                                       value="{{ $row['quantity'] }}" min="0">
                            </td>
                            <td>₦{{ number_format($line, 2) }}</td>
                            <td>
                                <button class="btn alt" form="remove-{{ md5($key) }}">Remove</button>
                            </td>
                        </tr>
                    @endforeach
                </table>

                <button class="btn alt" style="margin-top:15px">Update bag</button>
            </form>

            @foreach($cart as $key => $row)
                <form id="remove-{{ md5($key) }}" method="post" action="{{ route('cart.remove', $key) }}">
                    @csrf
                    @method('DELETE')
                </form>
            @endforeach

            <div style="margin-top:30px;font-size:20px">Total: <strong>₦{{ number_format($total, 2) }}</strong></div>

            @auth
                <a class="btn" style="margin-top:15px" href="{{ route('checkout') }}">Proceed to checkout</a>
            @else
                <a class="btn" style="margin-top:15px" href="{{ route('login') }}">Sign in to checkout</a>
            @endauth
        @endif
    </div>
</section>
@endsection
