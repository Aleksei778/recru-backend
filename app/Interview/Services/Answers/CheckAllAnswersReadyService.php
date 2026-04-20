<?php

declare(strict_types=1);

namespace App\Interview\Services\Answers;

use App\Interview\Jobs\EvaluateInterviewJob;
use App\Interview\Models\Interview;
use Illuminate\Support\Facades\DB;

final readonly class CheckAllAnswersReadyService
{
    public function __construct(
        
    ) {
    }

    public function checkAnswersReady(Interview $interview): void
    {
        $interviewId = $interview->id;

        DB::transaction(function () use ($intervieId) {
            $interview = Interview::lockForUpdate()->find($interview->id);

            if (!$interview->isProcessing()) {
                return;
            }

            $hasUnready = $interview->questions()
                ->where(function ($query) {
                    $query
                        ->whereDoesntHave('answer')
                        ->orWhereHas('answer', fn($q) => $q->whereNull('transcript'));
                })
                ->exists();

            if ($hasUnready) {
                return;
            }

            $interview->markAsEvaluating();

            EvaluateInterviewJob::dispatch($interview);
        });
    }
}
