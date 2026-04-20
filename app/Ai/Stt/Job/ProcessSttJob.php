<?php

namespace App\Ai\Stt\Job;

use App\Ai\Operation\Dto\Update;
use App\Ai\Operation\Enum\Status;
use App\Ai\Operation\Jobs\CheckOperationJob;
use App\Ai\Operation\Models\Operation;
use App\Ai\Operation\Services\CrudService;
use App\Ai\Stt\Contracts\AsyncInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class ProcessSttJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private Operation $operation
    ) {
    }

    public function handle(
        AsyncInterface $stt,
        CrudService $crudService,
    ): void {
        $providerId = $stt->recognizeAsync(
            $this->operation->subject->voiceLog->audio_path
        );

        if (!$providerId) {
            $updateOperationDto = new Update(
                status: Status::Failed,
            );
            $crudService->update($updateOperationDto, $this->operation);

            return;
        }

        $updateOperationDto = new Update(
            status: Status::InProgress,
            providerId: $providerId,
        );
        $crudService->update($updateOperationDto, $this->operation);

        CheckOperationJob::dispatch($this->operation->id)->delay(now()->addSeconds(10));
    }
}
