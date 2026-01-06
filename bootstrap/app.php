<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Illuminate\Foundation\Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // ✅ Daftarkan group 'web' & route middleware di sini

        // Middleware groups (opsional, Laravel default)
        $middleware->group('web', [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        // Middleware global (kalau kamu mau aktif di semua request)
        // $middleware->append(\App\Http\Middleware\TrustProxies::class);
        // $middleware->append(\Illuminate\Http\Middleware\HandleCors::class);

        // Route middleware (alias)
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'can' => \Illuminate\Auth\Middleware\Authorize::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,

            // ✅ Middleware kustom ASM PORTAL
            'active'  => \App\Http\Middleware\EnsureUserActive::class,
            'forcepw' => \App\Http\Middleware\ForcePasswordReset::class,
        ]);
    })
    ->withExceptions(function ($exceptions) {
        //
    })
    ->create();
