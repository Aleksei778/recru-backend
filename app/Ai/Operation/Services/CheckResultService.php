<?php

declare(strict_types=1);

namespace App\Ai\Operation\Services;

use App\Ai\Operation\Contracts\AsyncInterface as OperationAsyncInterface;
use App\Ai\Stt\Contracts\AsyncInterface as SttAsyncInterface;
use App\Ai\Operation\Enum\{CheckResult, Status, Type};
use App\Ai\Operation\Models\Operation;
use App\Ai\Operation\Repositories\Repository;
use App\Interview\Services\{EvaluationService, Questions\GenerateService, QuestionsService};
use App\Interview\Jobs\CheckAllAnswersReadyJob;
use App\Interview\Models\Answer;
use Psr\Log\LoggerInterface;

final readonly class CheckResultService
{
    public function __construct(
        private OperationAsyncInterface $operationInfo,
        private SttAsyncInterface $stt,
        private GenerateService $generateService,
        private Repository $operationRepository,
        private EvaluationService $evaluationService,
        private LoggerInterface $logger,
    ) {
    }

    public function check(int $operationId): CheckResult
    {
        $operation = $this->operationRepository->find($operationId);

        if (!$operation) {
            $this->logger->error("Operation $operationId not found");
            return CheckResult::Failed;
        }

        if ($operation->isCompleted()) {
            return CheckResult::AlreadyDone;
        }

        try {
            $data = $this->operationInfo->getInfoAboutProviderOperation($operation->provider_id);

            if (!$this->operationInfo->isDone($data)) {
                return CheckResult::NotReady;
            }

            if ($this->operationInfo->hasError($data)) {
                $operation->update([
                    'status' => Status::Failed,
                    'raw_response' => $data,
                ]);

                $this->logger->error("Operation {$operation->provider_id} failed", [
                    'error' => $data['error'] ?? null,
                ]);

                return CheckResult::Failed;
            }

            $raw = $this->stt->getRecognitionResult($operation->type, $data);

            $this->handleResult($operation, $raw);

            $operation->update([
                'status'       => Status::Completed,
                'raw_response' => $data,
                'result'       => $raw,
            ]);

            return CheckResult::Done;

        } catch (\Exception $e) {
            $this->logger->error("Error checking operation {$operation->provider_id}", [
                'message' => $e->getMessage(),
            ]);

            return CheckResult::Failed;
        }
    }

    private function handleResult(Operation $operation, string $raw): void
    {
        match ($operation->type) {
            Type::InterviewAnswersStt => $this->handleStt($operation, $raw),
            Type::InterviewEvaluationGpt => $this->evaluationService->handleResult($operation->subject, $raw),
            Type::InterviewQuestionsGenerationGpt => $this->questionsService->handleGenerationResult($operation->subject, $raw),
        };
    }

    private function handleStt(Operation $operation, string $transcript): void
    {
        /** @var Answer $answer */
        $answer = $operation->subject;

        $answer->update(['text' => $transcript]);

        CheckAllAnswersReadyJob::dispatch($answer->question->interview);
    }

    private function extractRaw(Type $type, array $data): string
    {
        return match ($type) {
            Type::InterviewAnswersStt => $this->extractSttText($data),
            default => $data['response']['alternatives'][0]['message']['text'] ?? '',
        };
    }

    private function extractSttText(array $data): string
    {
        $chunks = $data['response']['chunks'] ?? [];

        return trim(implode(' ', array_map(
            fn($chunk) => $chunk['alternatives'][0]['text'] ?? '',
            $chunks
        )));
    }
}
