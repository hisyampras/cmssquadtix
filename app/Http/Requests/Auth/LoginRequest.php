<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authenticate(): void
{
    $this->ensureIsNotRateLimited();

    if (! Auth::attempt($this->only('email','password'), $this->boolean('remember'))) {
        RateLimiter::hit($this->throttleKey());
        throw ValidationException::withMessages(['email' => __('auth.failed')]);
    }

    RateLimiter::clear($this->throttleKey());
}

public function ensureIsNotRateLimited(): void
{
    if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) return;

    $seconds = RateLimiter::availableIn($this->throttleKey());
    throw ValidationException::withMessages([
        'email' => __("Terlalu banyak percobaan. Coba lagi dalam :sec detik.", ['sec'=>$seconds]),
    ]);
}

public function throttleKey(): string
{
    return Str::transliterate(strtolower($this->input('email')).'|'.$this->ip());
}
}
