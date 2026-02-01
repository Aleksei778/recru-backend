<?php

declare(strict_types=1);

namespace App\Job\Enum;

enum EmploymentType: string
{
    case FULL_TIME = 'full_time';
    case PART_TIME = 'part_time';
    case CONTRACT = 'contract';
    case INTERNSHIP = 'internship';

    public static function values(): array
    {
        return [
            self::FULL_TIME,
            self::PART_TIME,
            self::CONTRACT,
            self::INTERNSHIP,
        ];
    }
}
