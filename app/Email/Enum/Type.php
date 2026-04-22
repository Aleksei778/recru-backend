<?php

declare(strict_types=1);

namespace App\Email\Enum;

enum Type: string
{
    case InterviewInvite = 'interview_invite';
    case QuestionsReady = 'questions_ready';
    case Results = 'results';
    case Reject = 'reject';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
