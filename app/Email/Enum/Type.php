<?php

declare(strict_types=1);

namespace App\Email\Enum;

enum Type: string
{
    case InterviewInvite = 'interview_invite';
    case QuestionsReady = 'questions_ready';
    case Reject = 'reject';
    case Approve = 'approve';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
