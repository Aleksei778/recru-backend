<?php

declare(strict_types=1);

namespace App\Ai\Yandex\Enum;

enum OperationType: string
{
    case STT = 'stt';
    case QUESTION_GENERATION = 'question_generation';
    case EVALUATION = 'evaluation';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
