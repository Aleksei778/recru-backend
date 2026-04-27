<?php

declare(strict_types=1);

namespace App\Ai\Gpt\Prompts\Resume;

use App\Ai\Contracts\Dto\GptMessage;

final readonly class EvaluationGenerator
{
    private function generate(): string
    {
        return <<<PROMPT
            Вы — опытный технический рекрутер. Оцените кандидата на основе текста его резюме.
            Дайте оценку по шкале от 1 до 100 и краткий текстовый фидбек (сильные стороны, слабые стороны, соответствие рынку).
            Верните ответ строго в формате JSON:
            {
              "score": 85,
              "feedback": "Текст фидбека"
            }
            Возвращайте ТОЛЬКО JSON без пояснений.
            PROMPT;
    }

    public function messages(string $text): array
    {
        $prompt = $this->generate();

        return [
            GptMessage::system($prompt),
            GptMessage::user("Текст резюме:\n\n" . $text),
        ];
    }
}
