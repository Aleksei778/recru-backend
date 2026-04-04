<?php

declare(strict_types=1);

namespace App\Email\Http\Controllers;

use App\Base\Http\Controllers\Controller as BaseController;
use App\Candidate\Models\Candidate;
use App\Email\Http\Requests\SendRequest;
use App\User\Models\User;
use App\Vacancy\Models\Vacancy;

final readonly class Controller extends BaseController
{
    public function send(SendRequest $request): void
    {
        $validated = $request->validated();

        $user = $request->user();
        $candidate = Candidate::query()->findOrFail($validated['candidate_id']);
        $vacancy = Vacancy::query()->findOrFail($validated['vacancy_id']);


    }

}
