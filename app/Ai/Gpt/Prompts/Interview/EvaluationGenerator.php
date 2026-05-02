<?php

declare(strict_types=1);

namespace App\Ai\Gpt\Prompts\Interview;

use App\Ai\Gpt\Dto\{Message as GptMessage};
use App\Interview\Models\Interview;
use App\Interview\Repositories\QuestionRepository;

final readonly class EvaluationGenerator
{
    public function __construct(
        private QuestionRepository $questionRepository,
    ) {
    }

    private function generate(Interview $interview): string
    {
        $questions = $this->questionRepository->findManyByInterviewWithAnswers($interview);

        $vacancy = $interview->vacancy;
        $candidate = $interview->candidate;

        $language = strtolower($candidate->locale->value) === 'ru'
            ? 'Русский'
            : 'Английский';

        $prompt = "Оцени ответы кандидата на интервью для вакансии '{$interview->vacancy->title}'.\n\n";

        $vacancySkillsStr = implode(', ', $vacancy->skills);
        $candidateSkillsStr = implode(', ', $candidate->skills);

        $prompt .= "Ты — профессиональный IT-рекрутер. Твоя задача — оценить ответы кандидата на вакансию: '$vacancy->title'.
            Описание вакансии: $vacancy->description. Скиллы, требуемые под вакансию: $vacancySkillsStr
            Скиллы кандидата: $candidateSkillsStr
            Интервью оценивается непредвзято, строго индивидуально под вакансию и кандидата, чтобы раскрыть его наилучшим образом
            Язык оценки: $language
           ";

        foreach ($questions as $q) {
            $answerText = $q->answer
                ? $q->answer->text
                : 'Ответ не был дан';

            $prompt .= "Вопрос: $q->text\nОтвет: $answerText\n\n";
        }

        $prompt .= "Дай оценку кандидату по 10-балльной шкале и краткий текстовый фидбек. (поясни почему именно столько баллов было набрано) 
            Формат ответа JSON: {\"grade\": 8, \"feedback\": \"Текст фидбека\"}";

        return $prompt;
    }

    public function messages(Interview $interview): array
    {
        $prompt = $this->generate($interview);

        return [
            new GptMessage(
                role: 'system',
                text: 'Ты эксперт по найму в IT. Оценивай ответы технически грамотно.'
            ),
            new GptMessage(
                role: 'user',
                text: $prompt
            ),
        ];
    }
}
