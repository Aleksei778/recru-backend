<?php

declare(strict_types=1);

namespace App\Candidate\Enum;

enum EducationLevel: string
{
    case SECONDARY = 'secondary';
    case INCOMPLETE_HIGHER = 'incomplete_higher';
    case BACHELOR = 'bachelor';
    case MASTER = 'master';
    case SPECIALIST = 'specialist';
    case DOCTOR = 'doctor';

    public static function values(): array
    {
        return [
            self::SECONDARY->value,
            self::INCOMPLETE_HIGHER->value,
            self::BACHELOR->value,
            self::MASTER->value,
            self::SPECIALIST->value,
            self::DOCTOR->value,
        ];
    }
}
