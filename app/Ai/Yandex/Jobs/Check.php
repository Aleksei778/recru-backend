<?php

declare(strict_types=1);

namespace App\Ai\Yandex\Jobs;

use App\Ai\Yandex\Services\Operation\CheckService;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use Illuminate\Contracts\Queue\{ShouldQueue, ShouldBeUnique};

final class Check implements ShouldQueue, ShouldBeUnique
{
    use Queueable, InteractsWithQueue, SerializesModels, Dispatchable;

    public function __construct()
    {
        $this->onQueue('operations');
    }

    public function handle(CheckService $checkService): void
    {
        $checkService->check();
    }
}
