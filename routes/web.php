<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ScanGateController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\UserAdminActionsController;

// redirect root → login (jika belum login)
Route::get('/', fn () => redirect()->route('login'))->middleware('guest');

// ======================
// 🔒 ROUTE UTAMA (USER LOGIN)
// ======================
Route::middleware(['auth', 'active', 'forcepw', 'restrict.scan-gate'])->group(function () {

    // HOME → dashboard scan
    Route::get('/', function () {
        $user = auth()->user();
        if ($user && method_exists($user, 'isScanGate') && $user->isScanGate()) {
            return redirect()->route('scan.mobile');
        }

        return redirect()->route('dashboard.index');
    })->name('home');

    // Dashboard (scan)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard/data', [DashboardController::class, 'data'])->name('dashboard.data');

    // Scan Gate
    Route::get('/scan', [ScanGateController::class, 'index'])->name('scan.index');
    Route::get('/scan/mobile', [ScanGateController::class, 'mobile'])
        ->middleware('can:scan-mobile')
        ->name('scan.mobile');
    Route::post('/scan', [ScanGateController::class, 'scan'])->name('scan.do');

    // Events
    Route::resource('events', EventController::class)->except(['show']);

    // ======================
    // 🎫 TICKETS (PER EVENT)
    // ======================
    Route::prefix('events/{event}')
        ->as('events.')
        ->group(function () {

            Route::get('/tickets',                [TicketController::class, 'index'])->name('tickets.index');
            Route::get('/tickets/create',         [TicketController::class, 'create'])->name('tickets.create');
            Route::post('/tickets',               [TicketController::class, 'store'])->name('tickets.store');
            Route::get('/tickets/bulk',           [TicketController::class, 'bulkForm'])->name('tickets.bulk.form');
            Route::post('/tickets/bulk',          [TicketController::class, 'bulkStore'])->name('tickets.bulk.store');
            Route::get('/tickets/template.csv',   [TicketController::class, 'downloadTemplate'])->name('tickets.template');
            Route::post('/tickets/type-policy',   [TicketController::class, 'upsertTypePolicy'])->name('tickets.type-policy.upsert');

            Route::get('/tickets/{ticket}/edit',  [TicketController::class, 'edit'])->name('tickets.edit');
            Route::put('/tickets/{ticket}',       [TicketController::class, 'update'])->name('tickets.update');
            Route::delete('/tickets/{ticket}',    [TicketController::class, 'destroy'])->name('tickets.destroy');
        });


    
});

// ======================
// 👤 PROFILE
// ======================
Route::middleware(['auth', 'restrict.scan-gate'])->group(function () {
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ======================
// 🛠️ ADMIN AREA
// ======================
Route::middleware(['auth', 'can:manage-users'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/users',              [UserManagementController::class, 'index'])->name('users.index');
        Route::get('/users/create',       [UserManagementController::class, 'create'])->name('users.create');
        Route::post('/users',             [UserManagementController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit',  [UserManagementController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}',       [UserManagementController::class, 'update'])->name('users.update');
        Route::patch('/users/{user}/role',[UserManagementController::class, 'updateRole'])->name('users.updateRole');
        Route::delete('/users/{user}',    [UserManagementController::class, 'destroy'])->name('users.destroy');

        Route::post('/users/{user}/reset-password', [UserAdminActionsController::class, 'sendResetLink'])->name('users.resetpw');
        Route::post('/users/{user}/force-reset',     [UserAdminActionsController::class, 'forceResetOnNextLogin'])->name('users.forcereset');
        Route::post('/users/{user}/suspend',         [UserAdminActionsController::class, 'suspend'])->name('users.suspend');
        Route::post('/users/{user}/activate',        [UserAdminActionsController::class, 'activate'])->name('users.unsuspend');
        Route::post('/users/{user}/impersonate',     [UserAdminActionsController::class, 'impersonate'])->name('users.impersonate');

        Route::post('/users/{user}/send-reset-link', [UserManagementController::class, 'sendResetLink'])
            ->name('users.sendResetLink');
    });

// stop impersonate (auth saja)
Route::middleware('auth')->group(function () {
    Route::post('/impersonate/stop', [UserAdminActionsController::class, 'stopImpersonate'])
        ->name('admin.impersonate.stop');
});

// AUTH ROUTES
require __DIR__ . '/auth.php';
