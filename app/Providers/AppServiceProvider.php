<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Ability untuk area admin user management
        Gate::define('manage-users', function ($user) {
            // pastikan model User kamu punya kolom 'role'
            return method_exists($user, 'isAdmin')
                ? $user->isAdmin()
                : ($user->role ?? null) === 'admin';
        });

        // (opsional) ability alias 'admin' agar kompatibel dengan kode lama
        Gate::define('admin', fn ($user) => ($user->role ?? null) === 'admin');
    }

    public function register(): void
    {
        $this->app->singleton(\App\Services\PremiProductionService::class, fn($app) => new \App\Services\PremiProductionService());
    }
}
