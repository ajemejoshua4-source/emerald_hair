@extends('layouts.app')

@section('content')
@include('admin.partials.nav')

<section class="section">
    <div class="wrap">
        <div class="eyebrow">Admin</div>
        <h2>Emerald Hair control room</h2>

        <div class="stats">
            <div class="stat">Products<strong>{{ $products }}</strong></div>
            <div class="stat">Orders<strong>{{ $orders }}</strong></div>
            <div class="stat">New enquiries<strong>{{ $enquiries }}</strong></div>
            <div class="stat">Paid revenue<strong>₦{{ number_format($revenue, 2) }}</strong></div>
        </div>

        <h3 style="margin-top:40px">Low stock</h3>
        @if($lowStock->isEmpty())
            <p class="muted">No products are running low.</p>
        @else
            <table class="table" style="max-width:640px">
                <tr><th>Product</th><th>Stock</th><th></th></tr>
                @foreach($lowStock as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->stock }}</td>
                        <td><a href="{{ route('admin.products.edit', $product) }}">Edit</a></td>
                    </tr>
                @endforeach
            </table>
        @endif
    </div>
</section>
@endsection
