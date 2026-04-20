<?php

declare(strict_types=1);

namespace App\Ai\Gpt\Prompts\Interview;

use App\Ai\Gpt\Dto\Message;
use App\Interview\Models\Interview;
use App\Interview\Repositories\QuestionRepository;

final readonly class EvalGenerator
{
    public function __construct(
        private QuestionRepository $questionRepository,
    ) {
    }

    private function generate(Interview $interview): string
    {
        $questions = $this->questionRepository->findManyByInterviewWithAnswers($interview);

        $prompt = "Оцени ответы кандидата на интервью для вакансии '{$interview->vacancy->title}'.\n\n";

        foreach ($questions as $q) {
            $answerText = $q->answer
                ? $q->answer->text
                : 'Ответ не был дан';

            $prompt .= "Вопрос: $q->text\nОтвет: $answerText\n\n";
        }

        $prompt .= "Дай оценку кандидату по 10-балльной шкале и краткий текстовый фидбек. 
            Формат ответа JSON: {\"grade\": 8, \"feedback\": \"Текст фидбека\"}";

        return $prompt;
    }

    public function messages(Interview $interview): array
    {
        $prompt = $this->generate($interview);

        return [
            Message::system('Ты эксперт по найму в IT. Оценивай ответы технически грамотно.'),
            Message::user($prompt),
        ];
    }
}
