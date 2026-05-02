<?php

declare(strict_types=1);

namespace App\Interview\Services;

use App\Ai\Gpt\Providers\GptInterface;
use App\Ai\Gpt\Prompts\Interview\EvaluationGenerator;
use App\Ai\Operation\Dto\Create;
use App\Ai\Operation\Enum\{Status, Type};
use App\Ai\Operation\Jobs\CheckOperationJob;
use App\Ai\Operation\Services\CrudService;
use App\Common\Enum\Locale;
use App\Email\Jobs\NotifyUserQuestionsReadyJob;
use App\Interview\Models\Interview;
use Psr\Log\LoggerInterface;

final readonly class EvaluationService
{
    public function __construct(
        private EvaluationGenerator $evaluationGenerator,
        private GptInterface $gptService,
        private CrudService $operationCrudService,
        private LoggerInterface $logger,
    ) {
    }

    public function evaluate(Interview $interview): bool
    {
        $externalId = $this->gptService->completion(
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

        CheckOperationJob::dispatch($operation->id)->delay(now()->addSeconds(3));

        return true;
    }

    public function handleEvaluationResult(Interview $interview, string $raw): void
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

        $hr = $interview->vacancy->createdBy;
        $locale = Locale::from(config('app.locale', 'ru'));

        NotifyUserQuestionsReadyJob::dispatch($interview, $hr, $locale);
    }

    private function markdownClean(string $response): string
    {
        $cleaned = preg_replace('/```json\s*|```\s*/', '', $response);
        return trim($cleaned);
    }
}
