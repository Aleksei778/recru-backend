<?php

declare(strict_types=1);

namespace App\Ai\Operation\Services;

use App\Ai\Stt\Providers\Yandex\Async;
use App\Ai\Operation\{
    Enum\CheckResult,
    Enum\Type,
    Models\Operation,
    Providers\OperationInterface,
    Repositories\Repository
};
use Psr\Log\LoggerInterface;
use App\Resume\Services\{
    EvaluationService as ResumeEvaluationService,
    ParseService as ResumeParseService
};
use App\Interview\Services\{
    Questions\GenerateService,
    Answers\GetAnswerFromSttOperationService,
    EvaluationService as InterviewEvaluationService
};

final readonly class CheckResultService
{
    public function __construct(
        private OperationInterface $operationInfo,
        private Async $sttService,
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
            $data = $this->operationInfo->getInfoAboutProviderOperation($operation);

            if (is_null($data)) {
                $this->logger->warning("Could not fetch operation data from provider", [
                    'operation_id' => $operationId,
                    'provider_id' => $operation->provider_id,
                ]);

                return CheckResult::NotReady;
            }

            $this->logger->info("Operation $operationId data", ['data' => $data]);

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

            $result = $this->handleResult($operation, $data);

            $operation->markAsCompleted($data, $result);

            return CheckResult::Done;

        } catch (\Throwable $e) {
            $this->logger->error("Error checking operation $operation->provider_id", [
                'message' => $e->getMessage(),
            ]);

            return CheckResult::Failed;
        }
    }

    private function handleResult(Operation $operation, array $data): string
    {
        if ($operation->type === Type::InterviewAnswersStt) {
            $result = $this->sttService->getResult($operation);
        } else {
            $result = $data['response']['alternatives'][0]['message']['text'] ?? null;
        }

        if (is_null($result)) {
            throw new \Exception('Operation result is null');
        }

        match ($operation->type) {
            Type::InterviewAnswersStt => $this->getAnswerFromSttOperationService->handleSttAnswerResult($operation, $result),
            Type::InterviewEvaluationGpt => $this->interviewEvaluationService->handleEvaluationResult($operation->subject, $result),
            Type::InterviewQuestionsGenerationGpt => $this->generateService->handleGenerationResult($operation->subject, $result),
            Type::ResumeEvaluationGpt => $this->resumeEvaluationService->handleEvaluationResult($operation->subject, $result),
            Type::ResumeParsingGpt => $this->resumeParseService->handleParsedResult($operation->subject, $result),
        };

        return $result;
    }
}
