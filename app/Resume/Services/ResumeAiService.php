<?php

declare(strict_types=1);

namespace App\Resume\Services;

use App\Ai\Yandex\Dto\Gpt\Message;
use App\Ai\Yandex\Services\Gpt\GptService;
use App\Resume\Dto\ParsedResumeDto;
use Psr\Log\LoggerInterface;

final readonly class ResumeAiService
{
    public function __construct(
        private GptService $gptService,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * Парсинг резюме с помощью YandexGPT.
     */
    public function parse(string $text): ?ParsedResumeDto
    {
        $systemPrompt = <<<PROMPT
Вы — эксперт по подбору ИТ-персонала. Ваша задача — извлечь структурированную информацию из текста резюме и вернуть её строго в формате JSON.
JSON должен содержать следующие поля:
- first_name (строка)
- last_name (строка)
- middle_name (строка или null)
- email (строка или null)
- phone (строка или null)
- linkedin_url (строка или null)
- github_url (строка или null)
- experience_years (целое число или null)
- grade (строка: junior, middle, senior, lead или null)
- education_level (строка или null)
- skills (массив строк)
- summary (краткое резюме, строка или null)

Если информация отсутствует, используйте null. Возвращайте ТОЛЬКО JSON без пояснений.
PROMPT;

        $messages = [
            new Message('system', $systemPrompt),
            new Message('user', "Текст резюме:\n\n" . $text),
        ];

        $response = $this->gptService->completion($messages, temperature: 0.1);

        if (!$response) {
            return null;
        }

        try {
            // Очистка ответа от возможных markdown-тегов
            $jsonStr = preg_replace('/^```json\s*|```$/', '', trim($response));
            $data = json_decode($jsonStr, true, 512, JSON_THROW_ON_ERROR);

            return new ParsedResumeDto(
                first_name: (string) ($data['first_name'] ?? ''),
                last_name: (string) ($data['last_name'] ?? ''),
                middle_name: $data['middle_name'] ?? null,
                email: $data['email'] ?? null,
                phone: $data['phone'] ?? null,
                linkedin_url: $data['linkedin_url'] ?? null,
                github_url: $data['github_url'] ?? null,
                experience_years: isset($data['experience_years']) ? (int) $data['experience_years'] : null,
                grade: $data['grade'] ?? null,
                education_level: $data['education_level'] ?? null,
                skills: $data['skills'] ?? [],
                summary: $data['summary'] ?? null,
            );
        } catch (\Exception $e) {
            $this->logger->error('Failed to parse resume JSON', [
                'error' => $e->getMessage(),
                'response' => $response,
            ]);
            return null;
        }
    }

    /**
     * AI оценка резюме.
     */
    public function assess(string $text): ?array
    {
        $systemPrompt = <<<PROMPT
Вы — опытный технический рекрутер. Оцените кандидата на основе текста его резюме.
Дайте оценку по шкале от 1 до 100 и краткий текстовый фидбек (сильные стороны, слабые стороны, соответствие рынку).
Верните ответ строго в формате JSON:
{
  "score": 85,
  "feedback": "Текст фидбека"
}
Возвращайте ТОЛЬКО JSON.
PROMPT;

        $messages = [
            new Message('system', $systemPrompt),
            new Message('user', "Текст резюме:\n\n" . $text),
        ];

        $response = $this->gptService->completion($messages, temperature: 0.2);

        if (!$response) {
            return null;
        }

        try {
            $jsonStr = preg_replace('/^```json\s*|```$/', '', trim($response));
            return json_decode($jsonStr, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Exception $e) {
            $this->logger->error('Failed to parse assessment JSON', [
                'error' => $e->getMessage(),
                'response' => $response,
            ]);
            return null;
        }
    }
}
