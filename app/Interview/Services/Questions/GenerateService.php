<?php

declare(strict_types=1);

namespace App\Interview\Services\Questions;

use App\Ai\Gpt\Providers\GptInterface;
use App\Ai\Gpt\Prompts\Interview\QuestionsGenerator;
use App\Ai\Operation\Dto\Create;
use App\Ai\Operation\Enum\Type;
use App\Ai\Operation\Jobs\CheckOperationJob;
use App\Ai\Operation\Services\CrudService as OperationCrudService;
use App\Interview\Models\Interview;
use Illuminate\Support\Facades\DB;
use Psr\Log\LoggerInterface;

 class GenerateService
{
    public function __construct(
        private QuestionsGenerator $questionsGenerator,
        private GptInterface $gptService,
        private CrudService $crudService,
        private LoggerInterface $logger,
        private OperationCrudService $operationCrudService,
    ) {
    }

    public function generate(Interview $interview): bool
    {
        $messages = $this->questionsGenerator->messages($interview);

        $providerId = $this->gptService->completion($messages, temperature: 0.6);

        if (!$providerId) {
            $this->logger->error('Failed to submit question generation', [
                'interview_id' => $interview->id,
            ]);

            return false;
        }

        $interview->markAsGeneratingQuestions();

        $operationDto = new Create(
            type: Type::InterviewQuestionsGenerationGpt,
            subjectId: $interview->id,
            subjectType: Interview::class,
            provider: config('ai.provider'),
            providerId: $providerId,
        );
        $operation = $this->operationCrudService->create($operationDto);

        CheckOperationJob::dispatch($operation->id)->delay(now()->addSeconds(3));

        return true;
    }

    public function handleGenerationResult(Interview $interview, string $response): void
    {
        $questions = $this->getQuestionsFromResponse($response);

        $this->saveQuestions($interview, $questions);

        $interview->markAsQuestionsReview();
    }

    private function getQuestionsFromResponse(string $response): array
    {
        return array_filter(explode("\n", $response));
    }

    private function saveQuestions(Interview $interview, array $questions): void
    {
        DB::transaction(function () use ($interview, $questions) {
            foreach ($questions as $index => $text) {
                $this->crudService->create(
                    interview: $interview,
                    text: trim($text),
                    number: $index + 1,
                );
            }
        });
    }
}
