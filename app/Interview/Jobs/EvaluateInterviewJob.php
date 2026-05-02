<?php

declare(strict_types=1);

namespace App\Interview\Jobs;

use App\Interview\Models\Interview;
use App\Interview\Services\EvaluationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};

final class EvaluateInterviewJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly Interview $interview
    ) {
    }

    public function handle(EvaluationService $evaluationService): void
    {
        $evaluationService->evaluate($this->interview);
    }
}
