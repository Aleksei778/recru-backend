<?php

declare(strict_types=1);

use App\Ai\Yandex\Http\Controllers\OperationController;
use App\Interview\Http\Controllers\Controller as InterviewController;
use App\Resume\Http\Controllers\ParseResumeController;
use App\User\Http\Controllers\Auth\{LoginController, RegisterController};
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [RegisterController::class, 'register']);
    Route::post('login', [LoginController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [LoginController::class, 'logout']);
        Route::get('me', [LoginController::class, 'getMe']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('interviews', Controller::class)->only(['index', 'create', 'show']);
    Route::apiResource('vacancies', \App\Vacancy\Http\Controllers\Controller::class);
    Route::apiResource('candidates', \App\Candidate\Http\Controllers\Controller::class);

    Route::prefix('resumes')->group(function () {
        Route::post('parse/file', [ParseResumeController::class, 'parseFile']);
        Route::post('parse/string', [ParseResumeController::class, 'parseString']);
    });

    Route::get('operations/{id}', [OperationController::class, 'show']);

    Route::prefix('emails')->group(function () {
        Route::post('send', [\App\Email\Http\Controllers\Controller::class, 'send']);
    });
});

Route::prefix('interviews')->group(function () {
    Route::post('{token}/start', [Controller::class, 'start']);
    Route::get('{interview}/next', [Controller::class, 'nextQuestion']);
    Route::post('questions/{question}/answer', [Controller::class, 'answer']);
});
