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
            self::SECONDARY,
            self::INCOMPLETE_HIGHER,
            self::BACHELOR,
            self::MASTER,
            self::SPECIALIST,
            self::DOCTOR,
        ];
    }
}
