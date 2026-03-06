<?php

use App\Http\Controllers\Api\MobileAuthController;
use App\Http\Controllers\Api\MobileMetaController;
use App\Http\Controllers\ScanGateController;
use Illuminate\Support\Facades\Route;

Route::prefix('mobile')->group(function () {
    Route::post('/login', [MobileAuthController::class, 'login']);

    Route::middleware(['auth.mobile-token', 'active.api'])->group(function () {
        Route::get('/me', [MobileAuthController::class, 'me']);
        Route::post('/logout', [MobileAuthController::class, 'logout']);
        Route::get('/events', [MobileMetaController::class, 'events']);
        Route::get('/group-gates', [MobileMetaController::class, 'groupGates']);
        Route::post('/scan', [ScanGateController::class, 'scan']);
    });
});
