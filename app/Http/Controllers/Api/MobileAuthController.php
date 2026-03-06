<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiToken;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MobileAuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:80'],
        ]);

        $user = User::query()->where('email', $data['email'])->first();
        if (!$user || !Hash::check($data['password'], (string) $user->password)) {
            return response()->json([
                'ok' => false,
                'message' => 'Email atau password salah.',
            ], 422);
        }

        if (strtolower((string) ($user->status ?? 'active')) === 'suspended') {
            return response()->json([
                'ok' => false,
                'message' => 'Akun disuspend.',
            ], 403);
        }

        $plainToken = Str::random(80);
        $now = Carbon::now('Asia/Jakarta');
        $expiresAt = $now->copy()->addDays(30);

        ApiToken::query()->create([
            'user_id' => $user->id,
            'name' => trim((string) ($data['device_name'] ?? 'android-app')) ?: 'android-app',
            'token_hash' => hash('sha256', $plainToken),
            'expires_at' => $expiresAt,
            'last_used_at' => $now,
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Login berhasil.',
            'token' => $plainToken,
            'token_type' => 'Bearer',
            'expires_at' => $expiresAt->toIso8601String(),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'ok' => true,
            'user' => [
                'id' => $user?->id,
                'name' => $user?->name,
                'email' => $user?->email,
                'role' => $user?->role,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $tokenId = (int) $request->attributes->get('mobile_token_id', 0);
        if ($tokenId > 0) {
            ApiToken::query()
                ->where('id', $tokenId)
                ->update(['revoked_at' => Carbon::now('Asia/Jakarta')]);
        }

        return response()->json([
            'ok' => true,
            'message' => 'Logout berhasil.',
        ]);
    }
}
