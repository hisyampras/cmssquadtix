<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForcePasswordReset
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() && $request->user()->force_password_reset) {
            return redirect()->route('password.request')
                ->with('status', 'Silakan reset password Anda sebelum melanjutkan.');
        }

        return $next($request);
    }
}
