@extends('layouts.app')

@section('content')
@include('admin.partials.nav')

@php
    use App\Models\Setting;
@endphp

<section class="section">
    <div class="wrap">
        <h2>Store settings</h2>

        <form class="form" method="post" action="{{ route('admin.settings.update') }}">
            @csrf
            @method('PUT')

            <div class="field">
                <label>Store email</label>
                <input type="email" name="store_email" value="{{ old('store_email', Setting::get('store_email', config('emerald.email'))) }}">
            </div>

            <div class="field">
                <label>Store phone</label>
                <input name="store_phone" value="{{ old('store_phone', Setting::get('store_phone')) }}">
            </div>

            <div class="field">
                <label>WhatsApp number (international format, digits only)</label>
                <input name="whatsapp" value="{{ old('whatsapp', Setting::get('whatsapp', config('emerald.whatsapp'))) }}">
            </div>

            <div class="field">
                <label>Instagram URL</label>
                <input name="instagram" value="{{ old('instagram', Setting::get('instagram', config('emerald.instagram'))) }}">
            </div>

            <div class="field">
                <label>Announcement bar text</label>
                <input name="announcement" value="{{ old('announcement', Setting::get('announcement')) }}">
            </div>

            <button class="btn">Save settings</button>
        </form>
    </div>
</section>
@endsection
