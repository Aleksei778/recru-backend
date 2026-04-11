<?php

declare(strict_types=1);

namespace App\Interview\Services;

use App\Ai\Prompts\Services\Interview\QuestionsGeneratorInterview;
use App\Ai\Yandex\Services\Gpt\GptService;
use App\Interview\Models\{Interview, Question};
use Illuminate\Support\Facades\DB;
use Psr\Log\LoggerInterface;

final readonly class QuestionsService
{
    public function __construct(
        private QuestionsGeneratorInterview $questionsGenerator,
        private GptService                  $gptService,
        private LoggerInterface             $logger,
    ) {
    }

    public function generate(Interview $interview): bool
    {
        $messages = $this->questionsGenerator->messages($interview);

        $response = $this->gptService->completion($messages);

        if (!$response) {
            $this->logger->error('Failed to generate questions for interview', ['interview_id' => $interview->id]);

            return false;
        }

        $questions = $this->getQuestionsFromResponse($response);

        $this->saveQuestions($interview, $questions);

        return true;
    }

    private function getQuestionsFromResponse(string $response): array
    {
        return array_filter(explode("\n", $response));
    }

    private function saveQuestions(Interview $interview, array $questions): void
    {
        DB::transaction(function () use ($interview, $questions) {
            foreach (array_slice($questions, 0, 10) as $index => $text) {
                Question::create([
                    'interview_id' => $interview->id,
                    'text' => trim($text),
                    'number' => $index + 1,
                ]);
            }
        });
    }
}
