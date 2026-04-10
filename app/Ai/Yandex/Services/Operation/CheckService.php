<?php

declare(strict_types=1);

namespace App\Ai\Yandex\Services\Operation;

use App\Ai\Yandex\Dto\Operation\Update;
use App\Ai\Yandex\Enum\OperationStatus;
use App\Ai\Yandex\Enum\OperationType;
use App\Ai\Yandex\Repositories\OperationRepository;
use App\Ai\Yandex\Services\Gpt\ResponseProcessingService as GptResponseProcessingService;
use App\Ai\Yandex\Services\Speechkit\ResponseProcessingService as SpeechkitResponseProcessingService;
use Psr\Log\LoggerInterface;
use App\Ai\Yandex\Services\Operation\ManageService as OperationService;

final readonly class CheckService
{
    public function __construct(
        private OperationService $operationService,
        private LoggerInterface $logger,
        private OperationRepository $operationRepository,
        private GptResponseProcessingService $gptResponseProcessingService,
        private SpeechkitResponseProcessingService $speechkitResponseProcessingService,
    ) {
    }

    public function check(): void
    {
        $operations = $this->operationRepository->findByStatuses([
            OperationStatus::NEW,
            OperationStatus::RESPONSE_RECEIVED_FAILED,
        ]);

        if (empty($operations)) {
            $this->logger->info('No operations found.');
            return;
        }

        foreach ($operations as $operation) {
            $this->logger->info("Checking operation $operation->yandex_id (Type: {$operation->type->value})...");
            try {
                $this->operationService->updateStatus($operation->id, OperationStatus::PROCESSING);

                $yandexData = $this->operationService->get($operation->yandex_id);

                if ($this->isCompleted($yandexData)) {
                    if ($this->isCompletedWithError($yandexData)) {
                        $this->operationService->updateWithLock(
                            new Update(
                                id: $operation->id,
                                status: OperationStatus::RESPONSE_RECEIVED_FAILED,
                                rawResponse: $yandexData,
                            )
                        );

                        $this->logger->error("Operation {$operation->yandex_id} completed with error.");
                        continue;
                    }

                    $result = $this->processResult($operation->type, $yandexData);

                    $this->operationService->updateWithLock(
                        new Update(
                            id: $operation->id,
                            status: OperationStatus::COMPLETED,
                            rawResponse: $yandexData,
                            result: ['content' => $result],
                        )
                    );

                    $this->logger->info("Operation {$operation->yandex_id} completed successfully.");

                    continue;
                }

                $this->operationService->updateStatus($operation->id, OperationStatus::NEW);

                $this->logger->info("Operation {$operation->yandex_id} is not completed.");
            } catch (\Exception $e) {
                $this->logger->error("Error checking operation $operation->yandex_id: {$e->getMessage()}");

                $this->operationService->updateStatus($operation->id, OperationStatus::NEW);
            }
        }
    }

    private function processResult(OperationType $type, array $data): string
    {
        return match ($type) {
            OperationType::STT => $this->speechkitResponseProcessingService->processResponse($data),
            OperationType::QUESTION_GENERATION, OperationType::EVALUATION => $this->gptResponseProcessingService->processResponse($data),
        };
    }

    private function isCompleted(?array $data): bool
    {
        return isset($data) &&
            array_key_exists('done', $data) &&
            $data['done'];
    }

    public function isCompletedWithError(array $data): bool
    {
        return array_key_exists('error', $data);
    }
}
