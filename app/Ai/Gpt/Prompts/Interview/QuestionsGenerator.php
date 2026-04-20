<?php

declare(strict_types=1);

namespace App\Ai\Gpt\Prompts\Interview;

use App\Ai\Contracts\Dto\GptMessage;
use App\Interview\Models\Interview;

final readonly class QuestionsGenerator
{
    private function generate(Interview $interview): string
    {
        $vacancy = $interview->vacancy;
        $candidate = $interview->candidate;

        return "Ты — профессиональный IT-рекрутер. Твоя задача — составить 10 вопросов для первичного интервью кандидата на вакансию: '{$vacancy->title}'.
            Описание вакансии: $vacancy->description.
            Вопросы должны проверять как hard skills, так и soft skills. 
            Выдай только список вопросов, каждый вопрос с новой строки, без номеров и лишнего текста.";
    }

    public function messages(Interview $interview): array
    {
        $prompt = $this->generate($interview);

        return [
            GptMessage::system('Ты помощник рекрутера, который составляет вопросы для интервью.'),
            GptMessage::user($prompt),
        ];
    }
}
