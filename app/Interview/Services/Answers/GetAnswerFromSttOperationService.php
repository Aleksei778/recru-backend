<?php

declare(strict_types=1);

namespace App\Interview\Services\Answers;

use App\Ai\Operation\Models\Operation;
use App\Interview\Jobs\CheckAllAnswersReadyJob;
use App\Interview\Models\Answer;

final readonly class GetAnswerFromSttOperationService
{
    public function handleSttAnswerResult(Operation $operation, string $text): void
    {
        /** @var Answer $answer */
        $answer = $operation->subject;
        $answer->setText($text);

        CheckAllAnswersReadyJob::dispatch($answer->question->interview);
    }
}
