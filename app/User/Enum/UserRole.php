<?php

declare(strict_types=1);

namespace App\User\Enum;

enum UserRole: string
{
    case ADMIN = 'admin';
    case HR = 'hr';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
