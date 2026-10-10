@extends('layouts.app')

@section('content')
@include('admin.partials.nav')

<section class="section">
    <div class="wrap">
        <h2>Customer enquiries</h2>

        <table class="table">
            <tr><th>Customer</th><th>Subject</th><th>Message</th><th>Status</th></tr>
            @forelse($enquiries as $e)
                <tr>
                    <td>{{ $e->name }}<br>{{ $e->email }}<br>{{ $e->phone }}</td>
                    <td>{{ $e->subject }}</td>
                    <td>{{ $e->message }}</td>
                    <td>
                        <form method="post" action="{{ route('admin.enquiries.update', $e) }}">
                            @csrf
                            @method('PATCH')
                            <select name="status">
                                @foreach(['new','read','resolved'] as $status)
                                    <option value="{{ $status }}" @selected($e->status === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                            <button>Save</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4">No enquiries yet.</td></tr>
            @endforelse
        </table>

        <div style="margin-top:20px">{{ $enquiries->links() }}</div>
    </div>
</section>
@endsection
