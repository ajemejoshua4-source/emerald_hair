@extends('layouts.app')

@section('content')
<section class="section">
    <div class="wrap" style="display:grid;grid-template-columns:1fr 1fr;gap:50px">
        <img class="pic" src="{{ asset($product->image) }}" alt="{{ $product->name }}">

        <div>
            <div class="eyebrow">{{ $product->category->name }}</div>
            <h1 style="font:500 48px Georgia,serif">{{ $product->name }}</h1>
            <p style="line-height:1.8">{{ $product->description }}</p>

            <div class="price" style="font-size:20px;margin:20px 0">
                @if($product->price_on_request && ! $product->hasVariants())
                    Price on request
                @elseif($product->hasVariants())
                    From ₦{{ number_format($product->fromPrice() ?? 0, 2) }}
                @else
                    ₦{{ number_format($product->price, 2) }}
                @endif
            </div>

            @if($product->hasVariants())
                <p class="muted">{{ $product->activeVariants->count() }} option(s) available</p>
            @endif

            @if(! $product->is_active)
                <p class="muted">Currently unavailable</p>
            @elseif($product->price_on_request && ! $product->hasVariants())
                <a class="btn" target="_blank" rel="noopener"
                   href="https://wa.me/{{ config('emerald.whatsapp') }}?text={{ urlencode('Hello Emerald Hair, I would like to enquire about '.$product->name.'.') }}">
                    Enquire on WhatsApp
                </a>
            @elseif($product->isPurchasable())
                <form method="post" action="{{ route('cart.add', $product) }}">
                    @csrf

                    @if($product->hasVariants())
                        <div class="field">
                            <label for="variant_id">Choose an option</label>
                            <select id="variant_id" name="variant_id" required>
                                @foreach($product->activeVariants as $variant)
                                    @if($variant->stock > 0)
                                        <option value="{{ $variant->id }}">
                                            {{ $variant->label }}
                                            @if($variant->unitPrice() !== null)
                                                — ₦{{ number_format($variant->unitPrice(), 2) }}
                                            @endif
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="field">
                        <label for="quantity">Quantity</label>
                        <input id="quantity" type="number" name="quantity" min="1" value="1">
                    </div>

                    <button class="btn">Add to bag</button>
                </form>
            @else
                <p class="muted">Currently out of stock</p>
                <a class="btn alt" target="_blank" rel="noopener"
                   href="https://wa.me/{{ config('emerald.whatsapp') }}?text={{ urlencode('Hello Emerald Hair, is '.$product->name.' available?') }}">
                    Ask about restock
                </a>
            @endif

            @auth
                <form method="post" action="{{ route('wishlist.toggle', $product) }}" style="margin-top:10px">
                    @csrf
                    <button class="btn alt">Save to wishlist</button>
                </form>
            @endauth
        </div>
    </div>
</section>

@if($related->isNotEmpty())
    <section class="section" style="border-top:1px solid var(--line)">
        <div class="wrap">
            <h2>You may also like</h2>
            <div class="grid">
                @foreach($related as $item)
                    <article class="card">
                        <a href="{{ route('products.show', $item) }}">
                            <img class="pic" src="{{ asset($item->image) }}" alt="{{ $item->name }}">
                        </a>
                        <div class="pad">
                            <h3>{{ $item->name }}</h3>
                            <div class="price">
                                {{ $item->price_on_request ? 'Price on request' : '₦'.number_format($item->price, 2) }}
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif
@endsection
