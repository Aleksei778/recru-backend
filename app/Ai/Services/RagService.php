<?php

namespace App\Ai\Services;

use App\Vacancy\Models\Vacancy;

final readonly class RagService
{
    public function __construct(
        private GigaChatService $gigaChatService,
    ) {
    }

    public function indexVacancy(Vacancy $vacancy): void
    {
        $vacancy->embeddings->delete();

    }
}
