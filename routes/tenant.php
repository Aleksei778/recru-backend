<?php

declare(strict_types=1);

use App\Tenant\Http\Controllers\Auth\LoginController;
use App\Tenant\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

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

//Route::middleware([
//    'web',
//    InitializeTenancyBySubdomain::class,
//    PreventAccessFromCentralDomains::class,
//])->group(function () {
//    Route::post('register', [RegisterController::class, 'register'])
//        ->name('register')
//        ->middleware('guest:tenant-web');
//
//    Route::post('login', [LoginController::class, 'login'])
//        ->name('login')
//        ->middleware('guest:tenant-web');
//
//    Route::post('logout', [LoginController::class, 'logout'])
//        ->name('logout')
//        ->middleware('auth:tenant-web');
//
//    Route::get('logout', [LoginController::class, 'user'])
//        ->name('user')
//        ->middleware('auth:tenant-web');
//});
