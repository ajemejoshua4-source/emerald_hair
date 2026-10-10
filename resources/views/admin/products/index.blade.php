@extends('layouts.app')

@section('content')
@include('admin.partials.nav')

<section class="section">
    <div class="wrap">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap">
            <h2 style="margin:0">Products</h2>
            <a class="btn" href="{{ route('admin.products.create') }}">Add product</a>
        </div>

        <form method="get" action="{{ route('admin.products') }}" style="display:flex;gap:8px;max-width:520px;margin:20px 0">
            <input name="q" value="{{ $q ?? '' }}" placeholder="Search products" style="flex:1">
            <button class="btn">Search</button>
        </form>

        <table class="table">
            <tr><th>Name</th><th>Category</th><th>Stock</th><th>Variants</th><th>Active</th><th></th></tr>
            @forelse($products as $p)
                <tr>
                    <td>{{ $p->name }}</td>
                    <td>{{ $p->category->name }}</td>
                    <td>{{ $p->stock }}</td>
                    <td>{{ $p->variants_count }}</td>
                    <td>{{ $p->is_active ? 'Yes' : 'No' }}</td>
                    <td style="white-space:nowrap">
                        <a href="{{ route('admin.products.edit', $p) }}">Edit</a>
                        <a href="{{ route('admin.variants', $p) }}" style="margin-left:8px">Variants</a>
                        <form style="display:inline;margin-left:8px" method="post" action="{{ route('admin.products.delete', $p) }}">
                            @csrf
                            @method('DELETE')
                            <button>Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">No products found.</td></tr>
            @endforelse
        </table>

        <div style="margin-top:20px">{{ $products->links() }}</div>
    </div>
</section>
@endsection
