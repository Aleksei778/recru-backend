<?php

declare(strict_types=1);

namespace App\Ai\Operation\Services;

use App\Ai\Operation\Enum\{CheckResult, Type};
use App\Ai\Operation\Models\Operation;
use App\Ai\Operation\Providers\OperationInterface as OperationAsyncInterface;
use App\Ai\Operation\Repositories\Repository;
use App\Interview\Services\Questions\GenerateService;
use Psr\Log\LoggerInterface;
use App\Resume\Services\{
    EvaluationService as ResumeEvaluationService,
    ParseService as ResumeParseService
};
use App\Interview\Services\{
    Answers\GetAnswerFromSttOperationService,
    EvaluationService as InterviewEvaluationService
};

final readonly class CheckResultService
{
    public function __construct(
        private OperationAsyncInterface $operationInfo,
        private GenerateService $generateService,
        private Repository $operationRepository,
        private InterviewEvaluationService $interviewEvaluationService,
        private ResumeEvaluationService $resumeEvaluationService,
        private ResumeParseService $resumeParseService,
        private LoggerInterface $logger,
        private GetAnswerFromSttOperationService $getAnswerFromSttOperationService,
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
                $operation->markAsFailed($data);

                $this->logger->error("Operation {$operation->provider_id} failed", [
                    'error' => $data['error'] ?? null,
                ]);

                return CheckResult::Failed;
            }

            $raw = $this->extractRaw($operation->type, $data);

            if (is_null($raw)) {
                $this->logger->error("Failed to extract raw result for operation $operation->provider_id");

                return CheckResult::Failed;
            }

            $this->handleResult($operation, $raw);

            $operation->markAsCompleted($data, $raw);

            return CheckResult::Done;

        } catch (\Exception $e) {
            $this->logger->error("Error checking operation $operation->provider_id", [
                'message' => $e->getMessage(),
            ]);

            return CheckResult::Failed;
        }
    }

    private function extractRaw(Type $type, array $data): ?string
    {
        return match ($type) {
            Type::InterviewAnswersStt => $this->extractSttText($data),
            default => $data['response']['alternatives'][0]['message']['text'] ?? null,
        };
    }

    private function handleResult(Operation $operation, string $raw): void
    {
        match ($operation->type) {
            Type::InterviewAnswersStt => $this->getAnswerFromSttOperationService->get($operation, $raw),
            Type::InterviewEvaluationGpt => $this->interviewEvaluationService->handleEvaluationResult($operation->subject, $raw),
            Type::InterviewQuestionsGenerationGpt => $this->generateService->handleGenerationResult($operation->subject, $raw),
            Type::ResumeEvaluationGpt => $this->resumeEvaluationService->handleEvaluationResult($operation->subject, $raw),
            Type::ResumeParsingGpt => $this->resumeParseService->handleParsedResult($operation->subject, $raw),
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
