<?php

declare(strict_types=1);

namespace App\Common\Enum;

enum Locale: string
{
    case RU = 'ru';
    case EN = 'en';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
