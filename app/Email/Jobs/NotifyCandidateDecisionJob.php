<?php

declare(strict_types=1);

namespace App\Email\Jobs;

use App\Common\Enum\Locale;
use App\Email\Services\SendService;
use App\Interview\Enum\Decision;
use App\Interview\Models\Interview;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};

final class NotifyCandidateDecisionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        private readonly Interview $interview,
        private readonly Decision $decision,
        private readonly Locale $locale,
    ) {
    }

    public function handle(SendService $sendService): void
    {
        $mailable = match ($this->decision) {
            Decision::Approve => $sendService->getCandidateApprovedMail($this->interview, $this->locale),
            Decision::Reject  => $sendService->getCandidateRejectedMail($this->interview, $this->locale),
        };

        $sendService->sendCandidateDecisionMail($mailable);
    }
}
