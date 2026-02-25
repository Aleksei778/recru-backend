<?php

declare(strict_types=1);

namespace App\Vacancy\Enum;

enum EmploymentType: string
{
    case FULL_TIME = 'full_time';
    case PART_TIME = 'part_time';
    case CONTRACT = 'contract';
    case INTERNSHIP = 'internship';

    public static function values(): array
    {
        return [
            self::FULL_TIME->value,
            self::PART_TIME->value,
            self::CONTRACT->value,
            self::INTERNSHIP->value,
        ];
    }
}
