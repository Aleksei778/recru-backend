<?php

declare(strict_types=1);

namespace App\Email\Jobs;

use App\Email\{
    Dto\Create,
    Enum\Type,
    Services\CrudService,
    Services\SendService
};
use App\Interview\Models\Interview;
use App\User\Models\User;
use Illuminate\{
    Bus\Queueable,
    Contracts\Queue\ShouldQueue,
    Foundation\Bus\Dispatchable,
    Queue\InteractsWithQueue,
    Queue\SerializesModels,
};

final class NotifyUserInterviewFinishedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        private readonly Interview $interview,
    ) {
    }

    public function handle(SendService $sendService, CrudService $crudService): void
    {
        $mailable = $sendService->getInterviewFinishedMail($this->interview);

        $body = $mailable->render();

        $crudService->create(new Create(
            senderId: null,
            interview: $this->interview,
            type: Type::InterviewFinished,
            subject: $mailable->subject,
            body: $body,
            recipientId: $this->interview->vacancy->created_by_id,
            recipientType: User::class,
            locale: $this->interview->vacancy->createdBy->locale,
        ));

        $sendService->sendInterviewFinishedMail($mailable);
    }
}
