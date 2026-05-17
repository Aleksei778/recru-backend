<?php

declare(strict_types=1);

namespace App\Interview\Services\Answers;

use App\Ai\Operation\Models\Operation;
use App\Interview\Jobs\CheckAllAnswersReadyJob;
use App\Interview\Models\Answer;

final readonly class GetAnswerFromSttOperationService
{
    public function __construct(
        private NormalizeAnswerService $normalizeAnswerService,
    ) {
    }

    public function handleSttAnswerResult(Operation $operation, string $text): void
    {
        /** @var Answer $answer */
        $answer = $operation->subject;

        $normalized = $this->normalizeAnswerService->normalize(
            question: $answer->question->text,
            rawText: $text,
        );

        $answer->setText($normalized);

        CheckAllAnswersReadyJob::dispatch($answer->question->interview);
    }
}
