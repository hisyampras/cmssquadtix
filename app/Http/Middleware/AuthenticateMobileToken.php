<?php

namespace App\Http\Middleware;

use App\Models\ApiToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateMobileToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $rawToken = trim((string) $request->bearerToken());
        if ($rawToken === '') {
            return response()->json([
                'ok' => false,
                'message' => 'Unauthorized.',
            ], 401);
        }

        $tokenHash = hash('sha256', $rawToken);
        $token = ApiToken::query()
            ->where('token_hash', $tokenHash)
            ->with('user')
            ->first();

        if (!$token || !$token->user || $token->isRevoked()) {
            return response()->json([
                'ok' => false,
                'message' => 'Unauthorized.',
            ], 401);
        }

        $now = Carbon::now('Asia/Jakarta');
        if ($token->isExpired($now)) {
            return response()->json([
                'ok' => false,
                'message' => 'Token expired.',
            ], 401);
        }

        $token->forceFill(['last_used_at' => $now])->save();
        $request->attributes->set('mobile_token_id', $token->id);
        Auth::setUser($token->user);
        $request->setUserResolver(fn () => $token->user);

        return $next($request);
    }
}
