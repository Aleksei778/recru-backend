<?php

declare(strict_types=1);

namespace App\Ai\Gpt\Prompts\Resume;

use App\Ai\Gpt\Dto\{Message as GptMessage};

final readonly class EvaluationGenerator
{
    private function generate(): string
    {
        return <<<PROMPT
            Вы — опытный технический рекрутер. Оцените кандидата на основе текста его резюме.
            Дайте оценку по шкале от 1 до 10 и краткий текстовый фидбек (сильные стороны, слабые стороны, соответствие рынку).
            Верните ответ строго в формате JSON:
            {
              "score": 4,
              "feedback": "Текст фидбека"
            }
            Возвращайте ТОЛЬКО JSON без пояснений.
            PROMPT;
    }

    public function messages(string $text): array
    {
        $prompt = $this->generate();

        return [
            new GptMessage(
                role: 'system',
                text: $prompt
            ),
            new GptMessage(
                role: 'user',
                text: "Текст резюме:\n\n" . $text
            ),
        ];
    }
}
