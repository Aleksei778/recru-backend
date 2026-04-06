<?php

declare(strict_types=1);

namespace App\Interview\Enum;

enum Status: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'inprogress';
    case COMPLETED = 'completed';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
