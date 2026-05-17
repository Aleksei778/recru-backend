<?php

declare(strict_types=1);

namespace App\Interview\Services\Answers;

use App\Ai\Gpt\Prompts\Interview\AnswerNormalizationGenerator;
use App\Ai\Gpt\Providers\Yandex\Sync as GptSync;
use Psr\Log\LoggerInterface;

final readonly class NormalizeAnswerService
{
    public function __construct(
        private GptSync $gpt,
        private AnswerNormalizationGenerator $generator,
        private LoggerInterface $logger,
    ) {
    }

    public function normalize(string $question, string $rawText): string
    {
        $messages = $this->generator->messages($question, $rawText);

        $normalized = $this->gpt->completion(
            messages: $messages,
            temperature: 0.1,
            maxTokens: 1000,
        );

        if (!$normalized) {
            $this->logger->warning('GPT answer normalization failed, using raw STT text', [
                'question' => $question,
            ]);

            return $rawText;
        }

        return trim($normalized);
    }
}
