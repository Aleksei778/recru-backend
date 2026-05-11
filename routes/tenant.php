<?php

declare(strict_types=1);

use App\Ai\Operation\Http\Controllers\Controller as OperationController;
use App\Candidate\Http\Controllers\Controller as CandidateController;
use App\Email\Http\Controllers\Controller as EmailController;
use App\Interview\Http\Controllers\Controller as InterviewController;
use App\Resume\Http\Controllers\Controller as  ResumeController;
use App\Vacancy\Http\Controllers\Controller as VacancyController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {
    Route::prefix('candidate/interviews')->group(function () {
        Route::get('{token}/questions/next', [InterviewController::class, 'nextQuestion']);
        Route::post('{token}/questions/{question}/answer', [InterviewController::class, 'answer']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('hr')->group(function () {
            Route::apiResource('interviews', InterviewController::class)
                ->only(['index', 'store', 'show']);

            Route::prefix('interviews/{interview}')->group(function () {
                Route::put('questions', [InterviewController::class, 'updateQuestions']);
                Route::post('questions/approve', [InterviewController::class, 'approveQuestions']);
                Route::post('close', [InterviewController::class, 'close']);
            });
        });

        Route::apiResource('vacancies', VacancyController::class);

        Route::get('candidates/search', [CandidateController::class, 'search']);
        Route::apiResource('candidates', CandidateController::class);

        Route::prefix('resume')->group(function () {
            Route::post('parse/file', [ResumeController::class, 'parseFile']);
            Route::post('parse/string', [ResumeController::class, 'parseString']);
            Route::post('save', [ResumeController::class, 'save']);
        });

        Route::get('operations/{operation}/status', [OperationController::class, 'status']);

        Route::prefix('emails')->group(function () {
            Route::get('inbox', [EmailController::class, 'indexInbox']);
            Route::get('sent', [EmailController::class, 'indexSent']);
            Route::get('{email}', [EmailController::class, 'show']);
            Route::post('send/invitation', [EmailController::class, 'sendInvitation']);
        });
    });
});
