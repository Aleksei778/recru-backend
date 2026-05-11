<?php

declare(strict_types=1);

namespace App\Interview\Jobs;

use App\Interview\Models\Interview;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};

final class MarkAsReadyJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, Dispatchable, SerializesModels;

    public function __construct(
        public Interview $interview,
    ) {
    }

    public function handle(): void
    {
        $this->interview->markAsReady();
    }
}
