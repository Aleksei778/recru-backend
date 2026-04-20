<?php

declare(strict_types=1);

namespace App\Resume\Services;

use App\Ai\Contracts\GptInterface;
use App\Ai\Gpt\Prompts\Resume\ParseGenerator;
use App\Resume\Models\Resume;

final readonly class ParseService
{
    public function __construct(
        private GptInterface $gptService,
        private ParseGenerator $parseGenerator,
        private SaveResultsService $saveResultsService,
    ) {
    }

    public function parse(string $text, Resume $resume): bool
    {
        $messages = $this->parseGenerator->messages($text);

        $response = $this->gptService->completion($messages, temperature: 0.1);

        if (!$response) {
            return false;
        }

        return $this->saveResultsService->saveParsedResult($resume, $response);
    }
}
