<?php

declare(strict_types=1);

namespace App\Ai\Operation\Enum;

enum Type: string
{
    case InterviewQuestionsTts = 'interview_questions_tts';
    case InterviewAnswersStt = 'interview_answers_stt';
    case InterviewQuestionsGenerationGpt = 'interview_questions_generation_gpt';
    case InterviewEvaluationGpt = 'interview_evaluation_gpt';
    case ResumeParsingGpt = 'resume_parsing_gpt';
    case ResumeEvaluationGpt = 'resume_evaluation_gpt';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
