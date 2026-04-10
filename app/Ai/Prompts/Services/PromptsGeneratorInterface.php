<?php

declare(strict_types=1);

namespace App\Ai\Prompts\Services;

use App\Interview\Models\Interview;

interface PromptsGeneratorInterface
{
    public function messages(Interview $interview): array;
}
