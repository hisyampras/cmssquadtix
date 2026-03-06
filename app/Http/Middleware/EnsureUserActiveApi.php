<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserActiveApi
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user && strtolower((string) ($user->status ?? 'active')) === 'suspended') {
            return response()->json([
                'ok' => false,
                'message' => 'Akun disuspend.',
            ], 403);
        }

        return $next($request);
    }
}
