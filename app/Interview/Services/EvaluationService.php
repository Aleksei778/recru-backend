<?php

declare(strict_types=1);

namespace App\Interview\Services;

use App\Ai\Gpt\Contracts\AsyncInterface;
use App\Ai\Operation\Dto\Create;
use App\Ai\Operation\Enum\Status;
use App\Ai\Operation\Enum\Type;
use App\Ai\Operation\Services\CrudService;
use App\Ai\Gpt\Prompts\Interview\EvalGenerator;
use App\Email\Jobs\QuestionsReadyNotifyUserJob;
use App\Interview\Models\Interview;
use Psr\Log\LoggerInterface;

final readonly class EvaluationService
{
    public function __construct(
        private EvalGenerator $evaluationGenerator,
        private AsyncInterface $gptService,
        private CrudService $operationCrudService,
        private LoggerInterface $logger,
    ) {
    }

    public function evaluate(Interview $interview): bool
    {
        $externalId = $this->gptService->completionAsync(
            messages: $this->evaluationGenerator->messages($interview)
        );

        if (!$externalId) {
            $this->logger->error('Failed to submit evaluation', [
                'interview_id' => $interview->id,
            ]);
            return false;
        }

        $operation = $this->operationCrudService->create(new Create(
            type: Type::InterviewEvaluationGpt,
            subjectId: $interview->id,
            subjectType: Interview::class,
            provider: config('ai.provider'),
            providerId: $externalId,
            status: Status::Pending,
        ));

        CheckOperation::dispatch($operation)->delay(now()->addSeconds(3));

        $interview->markAsEvaluating();

        return true;
    }

    public function handleResult(Interview $interview, string $raw): void
    {
        $result = json_decode(
            $this->markdownClean($raw),
            associative: true
        );

        if (!$result) {
            $this->logger->error('Failed to decode evaluation result', [
                'interview_id' => $interview->id,
            ]);
            return;
        }

        $interview->update([
            'grade' => $result['grade'] ?? 0,
            'text_grade' => $result['feedback'] ?? '',
        ]);

        $interview->markAsEvaluated();

        QuestionsReadyNotifyUserJob::dispatch($interview);
    }

    private function markdownClean(string $response): string
    {
        return preg_replace('/```json|```/', '', $response);
    }
}
