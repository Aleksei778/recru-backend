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

    public static function values(): array
    {
        return [
            self::HH->value,
            self::HABR->value,
            self::SOCIAL->value,
            self::EMAIL->value,
            self::BULK_IMPORT->value,
        ];
    }
}
