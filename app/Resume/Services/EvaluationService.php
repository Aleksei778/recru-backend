<?php

declare(strict_types=1);

namespace App\Resume\Services;

use App\Ai\Gpt\Providers\GptInterface;
use App\Ai\Gpt\Prompts\Resume\EvaluationGenerator;
use App\Ai\Operation\Dto\Create as OperationCreateDto;
use App\Ai\Operation\Enum\Status;
use App\Ai\Operation\Enum\Type;
use App\Ai\Operation\Jobs\CheckOperationJob;
use App\Common\Services\JsonDecoder;
use App\Resume\Models\Resume;
use App\Ai\Operation\Services\CrudService as OperationCrudService;

final readonly class EvaluationService
{
    public function __construct(
        private GptInterface $gptService,
        private EvaluationGenerator $evalGenerator,
        private JsonDecoder $jsonDecoder,
        private CrudService $crudService,
        private OperationCrudService $operationCrudService,
    ) {
    }

    public function evaluate(string $text, Resume $resume): int
    {
        $providerId = $this->gptService->completion(
            messages: $this->evalGenerator->messages($text)
        );

        $operation = $this->operationCrudService->create(new OperationCreateDto(
            type: Type::ResumeEvaluationGpt,
            subjectId: $resume->id,
            subjectType: Resume::class,
            provider: config('ai.provider'),
            providerId: $providerId,
            status: Status::Pending,
        ));

        CheckOperationJob::dispatch($operation->id)->delay(now()->addSeconds(3));

        return $operation->id;
    }

    public function handleEvaluationResult(Resume $resume, string $responseText): bool
    {
        $data = $this->jsonDecoder->decodeJson($responseText);

        if (!$data) {
            return false;
        }

        $this->crudService->updateGrade(
            resume: $resume,
            grade: isset($data['score']) ? (int) $data['score'] : 0,
            textGrade: $data['feedback'] ?? 'No feedback provided.',
        );

        return true;
    }
}
