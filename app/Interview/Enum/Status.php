<?php

declare(strict_types=1);

namespace App\Interview\Enum;

enum Status: string
{
    case Pending = 'pending';
    case GeneratingQuestions = 'generating_questions';
    case QuestionsReview = 'questions_review';
    case Synthesizing = 'synthesizing';
    case Ready = 'ready';
    case InProgress = 'in_progress';
    case Processing = 'processing';
    case Evaluating = 'evaluating';
    case Evaluated = 'evaluated';
    case Closed = 'closed';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
