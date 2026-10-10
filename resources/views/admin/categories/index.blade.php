@extends('layouts.app')

@section('content')
@include('admin.partials.nav')

<section class="section">
    <div class="wrap">
        <h2>Categories</h2>

        <form method="post" action="{{ route('admin.categories.store') }}" style="display:flex;gap:8px;max-width:700px;margin-bottom:20px">
            @csrf
            <input name="name" placeholder="New category" required>
            <input name="description" placeholder="Description">
            <button class="btn">Add</button>
        </form>

        <table class="table">
            <tr><th>Name</th><th>Products</th><th></th></tr>
            @foreach($categories as $c)
                <tr>
                    <td>{{ $c->name }}</td>
                    <td>{{ $c->products_count }}</td>
                    <td>
                        <form method="post" action="{{ route('admin.categories.delete', $c) }}">
                            @csrf
                            @method('DELETE')
                            <button>Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
</section>
@endsection
