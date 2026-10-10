@extends('layouts.app')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div>
            <img src="{{ $product->image ?? '/public/emerald.jpg' }}" class="w-full h-96 object-cover rounded" alt="{{ $product->name }}">
        </div>
        <div>
            <h1 class="text-3xl font-semibold">{{ $product->name }}</h1>
            <p class="mt-4 text-gray-600">{{ $product->description }}</p>
            <p class="mt-6 text-2xl font-bold">${{ number_format($product->price, 2) }}</p>
            <div class="mt-6">
                <button class="bg-emerald-600 text-white px-6 py-3 rounded">Add to Cart</button>
            </div>
        </div>
    </div>
@endsection
