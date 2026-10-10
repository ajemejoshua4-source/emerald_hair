<?php
namespace App\Http\Controllers; class AccountController extends Controller { public function index(){return view('account.index',['orders'=>auth()->user()->orders()->latest()->take(10)->get()]);} }
