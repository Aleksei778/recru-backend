<?php

declare(strict_types=1);

namespace App\Candidate\Enum;

enum Source: string
{
    case HH = 'hh';
    case HABR = 'habr';
    case SOCIAL = 'social';
    case EMAIL = 'email';
    case BULK_IMPORT = 'bulk_import';
    case GOOGLE_EXTENSION = 'google_extension';

    public static function values(): array
    {
        return [
            self::HH,
            self::HABR,
            self::SOCIAL,
            self::EMAIL,
            self::BULK_IMPORT,
            self::GOOGLE_EXTENSION,
        ];
    }
}
