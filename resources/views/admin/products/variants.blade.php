@extends('layouts.app')

@section('content')
@include('admin.partials.nav')

<section class="section">
    <div class="wrap">
        <a href="{{ route('admin.products') }}">&larr; Products</a>
        <h2>Variants — {{ $product->name }}</h2>
        <p class="muted">Use variants for length, lace type or colour options. The variant price overrides the product price.</p>

        <form method="post" action="{{ route('admin.variants.store', $product) }}"
              style="display:grid;grid-template-columns:2fr 1fr 1fr 1fr auto;gap:8px;align-items:end;margin:20px 0">
            @csrf
            <div class="field" style="margin:0">
                <label>Label</label>
                <input name="label" placeholder='e.g. 20" / HD lace' required>
            </div>
            <div class="field" style="margin:0">
                <label>SKU</label>
                <input name="sku">
            </div>
            <div class="field" style="margin:0">
                <label>Price</label>
                <input type="number" step="0.01" name="price">
            </div>
            <div class="field" style="margin:0">
                <label>Stock</label>
                <input type="number" name="stock" min="0" value="0" required>
            </div>
            <div>
                <input type="hidden" name="is_active" value="1">
                <button class="btn">Add variant</button>
            </div>
        </form>

        <table class="table">
            <tr><th>Label</th><th>SKU</th><th>Price</th><th>Stock</th><th>Active</th><th>Save</th></tr>
            @forelse($variants as $variant)
                <tr>
                    <td colspan="6" style="padding:0">
                        <form method="post" action="{{ route('admin.variants.update', $variant) }}"
                              style="display:grid;grid-template-columns:2fr 1fr 1fr 1fr 80px auto;gap:8px;padding:12px;align-items:center">
                            @csrf
                            @method('PUT')
                            <input name="label" value="{{ $variant->label }}" required>
                            <input name="sku" value="{{ $variant->sku }}">
                            <input type="number" step="0.01" name="price" value="{{ $variant->price }}">
                            <input type="number" name="stock" min="0" value="{{ $variant->stock }}" required>
                            <label style="font-size:13px">
                                <input type="checkbox" name="is_active" value="1" @checked($variant->is_active)> Active
                            </label>
                            <span style="white-space:nowrap">
                                <button>Save</button>
                                <button form="delete-variant-{{ $variant->id }}" style="margin-left:6px">Delete</button>
                            </span>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">No variants yet.</td></tr>
            @endforelse
        </table>

        @foreach($variants as $variant)
            <form id="delete-variant-{{ $variant->id }}" method="post" action="{{ route('admin.variants.delete', $variant) }}">
                @csrf
                @method('DELETE')
            </form>
        @endforeach
    </div>
</section>
@endsection
