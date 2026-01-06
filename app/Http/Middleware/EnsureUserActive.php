<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserActive
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() && $request->user()->status === 'suspended') {
            Auth::logout();
            return redirect()->route('login')->withErrors('Akun Anda disuspend. Hubungi administrator.');
        }
        return $next($request);
    }
}
