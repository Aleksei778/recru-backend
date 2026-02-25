<?php

declare(strict_types=1);

namespace App\Vacancy\Enum;

enum WorkMode: string
{
    case OFFICE = 'office';
    case REMOTE = 'remote';
    case HYBRID = 'hybrid';

    public static function values(): array
    {
        return [
            self::OFFICE->value,
            self::REMOTE->value,
            self::HYBRID->value,
        ];
    }
}
