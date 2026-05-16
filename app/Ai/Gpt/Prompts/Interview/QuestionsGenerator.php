<?php

declare(strict_types=1);

namespace App\Ai\Gpt\Prompts\Interview;

use App\Ai\Gpt\Dto\{Message as GptMessage};
use App\Interview\Models\Interview;

 class QuestionsGenerator
{
    private function generate(Interview $interview): string
    {
        $vacancy = $interview->vacancy;
        $candidate = $interview->candidate;

        $language = strtolower($candidate->locale->value) === 'ru'
            ? 'Русский'
            : 'Английский';

        $vacancySkillsStr = $vacancy->skills->pluck('name')->implode(', ');
        $candidateSkillsStr = $candidate->skills->pluck('name')->implode(', ');

        $candidateGrade = $candidate->grade?->value;
        $vacancyGrade = $vacancy->grade?->value;

        return "Ты — профессиональный IT-рекрутер. Твоя задача — составить {$interview->questions_number} вопросов для первичного интервью кандидата на вакансию: '{$vacancy->title}'.
            Описание вакансии: $vacancy->description
            Скиллы кандидата: $candidateSkillsStr. Скиллы, требуемые под вакансию: $vacancySkillsStr
            Уровень кандидата: $candidateGrade. Уровень, требуемый в рамках вакансии: $vacancyGrade
            Интервью составляется строго индивидуально под вакансию и кандидата, чтобы раскрыть его наилучшим образом
            Вопросы должны проверять как hard skills, так и soft skills (но первое - в большей мере).
            Выдай только список вопросов, каждый вопрос с новой строки, без номеров и лишнего текста.
            Также вставляй прямо задачи с кодом (но без написания кода!!! то есть те что можно словесно разобрать).
            Язык вопросов: $language
           ";
    }

    public function messages(Interview $interview): array
    {
        $prompt = $this->generate($interview);

        return [
            new GptMessage(
                role: 'system',
                text: 'Ты помощник рекрутера, который составляет вопросы для интервью.'
            ),
            new GptMessage(
                role: 'user',
                text: $prompt
            ),
        ];
    }
}
