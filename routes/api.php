<?php

declare(strict_types=1);

use App\User\Http\Controllers\Auth\{LoginController, RegisterController};
use Illuminate\Support\Facades\Route;
use App\Skill\Http\Controllers\Controller as SkillController;

Route::prefix('auth')->group(function () {
    Route::post('register', [RegisterController::class, 'register']);
    Route::post('login', [LoginController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [LoginController::class, 'logout']);
        Route::get('me', [LoginController::class, 'getMe']);
    });
});

Route::get('skills', [SkillController::class, 'index']);
