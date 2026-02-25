<?php

declare(strict_types=1);

namespace App\Vacancy\Enum;

enum Status: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case CLOSED = 'closed';

    public static function values(): array
    {
        return [
            self::DRAFT->value,
            self::PUBLISHED->value,
            self::CLOSED->value,
        ];
    }
}
