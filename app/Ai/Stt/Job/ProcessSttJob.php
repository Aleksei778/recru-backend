<?php

declare(strict_types=1);

namespace App\Ai\Stt\Job;

use App\Common\Enum\Locale;
use App\Ai\Operation\{
    Dto\Update as OperationUpdate,
    Enum\Status as OperationStatus,
    Jobs\CheckOperationJob,
    Models\Operation,
    Services\CrudService as OperationCrudService,
};
use App\Ai\Stt\Providers\SttInterface;
use Illuminate\{
    Bus\Queueable as Queueable,
    Contracts\Queue\ShouldQueue,
    Foundation\Bus\Dispatchable,
    Queue\InteractsWithQueue,
    Queue\SerializesModels
};

final class ProcessSttJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly Operation $operation,
        private readonly Locale $locale,
    ) {
    }

    public function handle(
        SttInterface $stt,
        OperationCrudService $crudService
    ): void {
        $providerId = $stt->recognize(
            audioPath: $this->operation->subject->voiceLog->audio_path
        );

        if (!$providerId) {
            $updateOperationDto = new OperationUpdate(
                status: OperationStatus::Failed,
            );
            $crudService->update($updateOperationDto, $this->operation);

            return;
        }

        $updateOperationDto = new OperationUpdate(
            status: OperationStatus::InProgress,
            providerId: $providerId,
        );
        $crudService->update($updateOperationDto, $this->operation);

        CheckOperationJob::dispatch($this->operation->id)->delay(now()->addSeconds(10));
    }
}
