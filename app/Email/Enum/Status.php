<?php

declare(strict_types=1);

namespace App\Email\Enum;

enum Status: string
{
    case Pending = 'pending';
    case Sent = 'sent';
    case Failed = 'failed';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
