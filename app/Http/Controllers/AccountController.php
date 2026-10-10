<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index()
    {
        return view('account.index', [
            'orders' => Auth::user()->orders()->with('items')->latest()->take(10)->get(),
        ]);
    }
}
