<?php
namespace App\Http\Middleware;
use Closure; use Illuminate\Http\Request;
class AdminMiddleware { public function handle(Request $request, Closure $next) { if (!$request->user() || !$request->user()->is_admin) return redirect()->route('login')->with('error','Administrator access required.'); return $next($request); } }
