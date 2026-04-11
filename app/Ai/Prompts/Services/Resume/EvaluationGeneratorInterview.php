<?php

namespace App\Ai\Prompts\Services\Resume;

use App\Ai\Prompts\Services\Interview\InterviewPromptsGeneratorInterface;
use App\Ai\Yandex\Dto\Gpt\Message;
use App\Interview\Models\Interview;

final readonly class EvaluationGeneratorInterview implements InterviewPromptsGeneratorInterface
{
    private function generate(Interview $interview): string
    {

    }

    public function messages(Interview $interview): array
    {
        $prompt = $this->generate($interview);

        return [
            new Message('system', 'Ты эксперт по найму в IT. Оценивай ответы технически грамотно.'),
            new Message('user', $prompt),
        ];
    }
}
