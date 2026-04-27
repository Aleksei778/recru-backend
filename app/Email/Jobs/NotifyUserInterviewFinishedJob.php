<?php

declare(strict_types=1);

namespace App\Email\Jobs;

use App\Common\Enum\Locale;
use App\Email\Services\SendService;
use App\Interview\Models\Interview;
use App\User\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};

final class NotifyUserInterviewFinishedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        private readonly Interview $interview,
        private readonly User $hr,
        private readonly Locale $locale,
    ) {
    }

    public function handle(SendService $sendService): void
    {
        $mailable = $sendService->getInterviewFinishedMail($this->interview, $this->hr, $this->locale);

        $sendService->sendInterviewFinishedMail($mailable);
    }
}
