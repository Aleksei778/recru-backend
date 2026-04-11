<?php

declare(strict_types=1);

namespace App\Ai\Prompts\Services\Resume;

use App\Interview\Models\Interview;

interface ResumePromptsGeneratorInterface
{
    public function messages(string $text): array;
}
