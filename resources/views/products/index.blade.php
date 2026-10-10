@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-semibold">Shop</h1>
    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($products as $product)
            <div class="border rounded-md p-4">
                <img src="{{ $product->image ?? '/public/emerald.jpg' }}" class="w-full h-48 object-cover rounded" alt="{{ $product->name }}">
                <h3 class="mt-4 font-semibold">{{ $product->name }}</h3>
                <p class="text-gray-600">${{ number_format($product->price, 2) }}</p>
                <a href="{{ route('products.show', $product->slug) }}" class="mt-3 inline-block text-emerald-600">View</a>
            </div>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $products->links() }}
    </div>
@endsection
