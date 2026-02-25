<?php

declare(strict_types=1);

namespace App\User\Enum;

enum UserRole: string
{
    case Admin = 'admin';
    case Recruiter = 'recruiter';

    public static function values(): array
    {
        return [
            self::Admin->value,
            self::Recruiter->value,
        ];
    }
}
