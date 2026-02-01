<?php

declare(strict_types=1);

namespace App\Job\Enum;

enum Status: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case CLOSED = 'closed';

    public static function values(): array
    {
        return [
            self::DRAFT,
            self::PUBLISHED,
            self::CLOSED,
        ];
    }
}
