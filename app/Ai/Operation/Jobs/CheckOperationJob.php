<?php

declare(strict_types=1);

namespace App\Ai\Operation\Jobs;

use App\Ai\Operation\Enum\CheckResult;
use App\Ai\Operation\Services\CheckResultService;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{SerializesModels, InteractsWithQueue};
use Illuminate\Contracts\Queue\{ShouldBeUnique, ShouldQueue};

final class CheckOperationJob implements ShouldQueue, ShouldBeUnique
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

        if ($result === CheckResult::NotReady) {
            $this->release($this->backoff);
        }
    }
}
