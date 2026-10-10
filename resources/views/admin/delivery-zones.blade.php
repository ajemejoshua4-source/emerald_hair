@extends('layouts.app')

@section('content')
@include('admin.partials.nav')

<section class="section">
    <div class="wrap">
        <h2>Delivery zones</h2>
        <p class="muted">Delivery fees shown at checkout. Leave no zones to offer WhatsApp-only delivery arrangement.</p>

        <form method="post" action="{{ route('admin.delivery-zones.store') }}"
              style="display:flex;gap:8px;max-width:640px;margin:20px 0">
            @csrf
            <input name="name" placeholder="Zone name (e.g. Lagos Mainland)" required style="flex:1">
            <input type="number" step="0.01" name="fee" placeholder="Fee" min="0" required>
            <button class="btn">Add zone</button>
        </form>

        <table class="table" style="max-width:720px">
            <tr><th>Zone</th><th>Fee</th><th>Active</th><th></th></tr>
            @forelse($zones as $zone)
                <tr>
                    <td>{{ $zone->name }}</td>
                    <td>₦{{ number_format($zone->fee, 2) }}</td>
                    <td>{{ $zone->is_active ? 'Yes' : 'No' }}</td>
                    <td>
                        <form method="post" action="{{ route('admin.delivery-zones.delete', $zone) }}">
                            @csrf
                            @method('DELETE')
                            <button>Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4">No delivery zones yet.</td></tr>
            @endforelse
        </table>
    </div>
</section>
@endsection
