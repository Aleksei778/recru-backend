<?php

declare(strict_types=1);

namespace App\Interview\Jobs;

use App\Interview\Enum\Status;
use App\Interview\Models\Interview;
use App\Interview\Services\EvaluationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

final class CheckAllAnswersReadyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly Interview $interview) {}

    public function handle(EvaluationService $evaluationService): void
    {
        DB::transaction(function () use ($evaluationService) {
            $interview = Interview::lockForUpdate()->find($this->interview->id);

            if ($interview->status !== Status::Processing) {
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
