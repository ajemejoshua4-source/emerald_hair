@extends('layouts.app')

@section('content')
@include('admin.partials.nav')

<section class="section">
    <div class="wrap">
        <h2>Orders</h2>

        <form method="get" action="{{ route('admin.orders') }}" style="display:flex;gap:8px;max-width:520px;margin-bottom:20px">
            <input name="q" value="{{ $q ?? '' }}" placeholder="Search order number, name or phone" style="flex:1">
            <button class="btn">Search</button>
        </form>

        <table class="table">
            <tr><th>Order</th><th>Customer</th><th>Total</th><th>Status</th><th>Payment</th><th>Save</th></tr>
            @forelse($orders as $o)
                <tr>
                    <td>{{ $o->order_number }}</td>
                    <td>{{ $o->customer_name }}<br>{{ $o->phone }}</td>
                    <td>₦{{ number_format($o->total, 2) }}</td>
                    <td colspan="3">
                        <form method="post" action="{{ route('admin.orders.update', $o) }}" style="display:flex;gap:8px;flex-wrap:wrap">
                            @csrf
                            @method('PATCH')
                            <select name="status">
                                @foreach(['pending','confirmed','processing','shipped','completed','cancelled'] as $status)
                                    <option value="{{ $status }}" @selected($o->status === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                            <select name="payment_status">
                                @foreach(['unpaid','paid','refunded'] as $payment)
                                    <option value="{{ $payment }}" @selected($o->payment_status === $payment)>{{ ucfirst($payment) }}</option>
                                @endforeach
                            </select>
                            <button>Save</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">No orders found.</td></tr>
            @endforelse
        </table>

        <div style="margin-top:20px">{{ $orders->links() }}</div>
    </div>
</section>
@endsection
