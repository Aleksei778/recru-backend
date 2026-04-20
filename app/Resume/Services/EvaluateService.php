<?php

declare(strict_types=1);

namespace App\Resume\Services;

use App\Ai\Contracts\GptInterface;
use App\Ai\Gpt\Prompts\Resume\EvalGenerator;
use App\Resume\Models\Resume;

final readonly class EvaluateService
{
    public function __construct(
        private GptInterface $gptService,
        private EvalGenerator $evalGenerator,
        private SaveResultsService $saveResultsService,
    ) {
    }

    public function evaluate(string $text, Resume $resume): bool
    {
        $messages = $this->evalGenerator->messages($text);

        $response = $this->gptService->completion($messages, temperature: 0.2);

        if (!$response) {
            return false;
        }

        return $this->saveResultsService->saveEvaluationResult($resume, $response);
    }
}
