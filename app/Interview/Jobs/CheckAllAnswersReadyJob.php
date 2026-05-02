<?php

declare(strict_types=1);

namespace App\Interview\Jobs;

use App\Interview\Models\Interview;
use App\Interview\Services\Answers\CheckAllAnswersReadyService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};

final class CheckAllAnswersReadyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private Interview $interview
    ) {
    }

    public function handle(CheckAllAnswersReadyService $service): void
    {
        $service->checkAnswersReady($this->interview);
    }
}
