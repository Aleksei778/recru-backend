<?php

declare(strict_types=1);

namespace App\Ai\Gpt\Prompts\Interview;

use App\Ai\Gpt\Dto\Message as GptMessage;

class AnswerNormalizationGenerator
{
    public function messages(string $question, string $rawAnswer): array
    {
        return [
            new GptMessage(
                role: 'system',
                text: 'Ты ассистент по нормализации текста. Тебе дают вопрос интервью и ответ кандидата, распознанный из голоса. Исправь ошибки распознавания: технические термины, аббревиатуры, пунктуацию, связность предложений. Не добавляй новый смысл, не сокращай и не расширяй ответ. Верни только исправленный текст ответа без каких-либо пояснений.',
            ),
            new GptMessage(
                role: 'user',
                text: "Вопрос: $question\n\nОтвет кандидата (распознан из голоса): $rawAnswer",
            ),
        ];
    }
}
