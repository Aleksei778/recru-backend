<?php

declare(strict_types=1);

namespace App\Ai\Prompts\Services\Interview;

use App\Interview\Models\Interview;

interface InterviewPromptsGeneratorInterface
{
    public function messages(Interview $interview): array;
}
