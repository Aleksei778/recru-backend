<?php

declare(strict_types=1);

namespace App\Ai\Operation\Jobs;

use App\Ai\Operation\Enum\CheckResult;
use App\Ai\Operation\Services\CheckResultService;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{SerializesModels, InteractsWithQueue};
use Illuminate\Contracts\Queue\ShouldQueue;

final class CheckOperationJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, Dispatchable, SerializesModels;

    public int $tries = 20;
    public int $backoff = 10;

    public function __construct(
        public int $operationId,
    ) {
    }

    public function handle(CheckResultService $service): void
    {
        $result = $service->check($this->operationId);

        if (in_array($result, [CheckResult::NotReady, CheckResult::Failed], true)) {
            $this->release($this->backoff);
        }
    }
}
