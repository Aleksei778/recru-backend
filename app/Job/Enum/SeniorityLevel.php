<?php

declare(strict_types=1);

namespace App\Job\Enum;

enum SeniorityLevel: string
{
    case TRAINEE = 'trainee';
    case JUNIOR = 'junior';
    case MIDDLE = 'middle';
    case SENIOR = 'senior';
    case LEAD = 'lead';

    public static function values(): array
    {
        return [
            self::TRAINEE,
            self::JUNIOR,
            self::MIDDLE,
            self::SENIOR,
            self::LEAD,
        ];
    }
}
