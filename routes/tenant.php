<?php

declare(strict_types=1);

use App\Http\Tenant\Controllers\AdminSettingsController;
use App\Http\Tenant\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Tenant\Controllers\BotController;
use App\Http\Tenant\Controllers\BotWebhookController;
use App\Http\Tenant\Middleware\HandleInertiaRequests;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use App\Http\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyBySubdomain::class,
    PreventAccessFromCentralDomains::class,
    HandleInertiaRequests::class,
])->group(function () {
    Route::get('/', [BotController::class, 'index'])
        ->name('bot')
        ->middleware('auth:tenant-web');
    Route::get('/bot/{botId}', [BotController::class, 'view'])
        ->name('bot.view')
        ->middleware('auth:tenant-web');
    Route::get('/bot/{botId}/dashboard', [BotController::class, 'dashboard'])
        ->name('bot.dashboard')
        ->middleware('auth:tenant-web');
    Route::post('/bot/create', [BotController::class, 'create'])
        ->name('bot.create')
        ->middleware('auth:tenant-web');
    Route::post('/{token}/webhook', [BotWebhookController::class, 'handleWebhook'])
        ->name('telegram.webhook');

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login')
        ->middleware('guest');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])
        ->name('login.store')
        ->middleware('guest');
    Route::get('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout')
        ->middleware('auth:tenant-web');

    Route::get('/settings', [AdminSettingsController::class, 'index'])
        ->name('settings')
        ->middleware('auth:tenant-web');
    Route::post('/settings', [AdminSettingsController::class, 'store'])
        ->name('settings.update')
        ->middleware('auth:tenant-web');
});
