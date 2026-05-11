<?php

declare(strict_types=1);

namespace App\Ai\Gpt\Prompts\Resume;

use App\Ai\Gpt\Dto\{Message as GptMessage};

final readonly class ParseGenerator
{
    private function generate(): string
    {
        return <<<PROMPT
            Вы — эксперт по подбору ИТ-персонала. Ваша задача — извлечь структурированную информацию из текста резюме и вернуть её строго в формате JSON.
            JSON должен содержать следующие поля:
            - first_name (строка)
            - last_name (строка)
            - middle_name (строка или null)
            - email (строка или null)
            - phone (строка или null)
            - socials (массив объектов: [{"name": "linkedin", "url": "..."}, {"name": "github", "url": "..."}] или [])
            - experience_years (число с плавающей точкой или null)
            - workplaces (массив объектов: [{"company_name": "...", "position": "...", "description": "...", "started_at": "YYYY-MM-DD", "ended_at": "YYYY-MM-DD" или null}] или [])
            - education_level (строка или null)
            - skills (массив строк)
            - summary (краткое резюме, строка или null)
            
            Если информация отсутствует, используйте null или пустой массив. Возвращайте ТОЛЬКО JSON без пояснений.
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
