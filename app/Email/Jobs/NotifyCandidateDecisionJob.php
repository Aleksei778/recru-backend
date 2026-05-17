<?php

declare(strict_types=1);

namespace App\Email\Jobs;

use App\Candidate\Models\Candidate;
use App\Common\Enum\Locale;
use App\Email\{
    Dto\Create,
    Enum\Type,
    Services\CrudService,
    Services\SendService
};
use App\Interview\{
    Enum\Decision,
    Models\Interview
};
use Illuminate\{
    Bus\Queueable,
    Foundation\Bus\Dispatchable,
    Contracts\Queue\ShouldQueue,
    Queue\InteractsWithQueue,
    Queue\SerializesModels,
};

final class NotifyCandidateDecisionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        private readonly Interview $interview,
        private readonly Decision $decision,
    ) {
    }

    public function handle(SendService $sendService, CrudService $crudService): void
    {
        $mailable = match ($this->decision) {
            Decision::Approve => $sendService->getCandidateApprovedMail($this->interview),
            Decision::Reject => $sendService->getCandidateRejectedMail($this->interview),
        };

        $type = match ($this->decision) {
            Decision::Approve => Type::Approve,
            Decision::Reject => Type::Reject,
        };

        $body = $mailable->render();

        $crudService->create(new Create(
            senderId: $this->interview->vacancy->created_by_id,
            interview: $this->interview,
            type: $type,
            subject: $mailable->subject,
            body: $body,
            recipientId: $this->interview->candidate->id,
            recipientType: Candidate::class,
            locale: $this->interview->candidate->locale ?? Locale::RU,
        ));

        $sendService->sendCandidateDecisionMail($mailable);
    }
}
