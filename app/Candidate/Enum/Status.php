<?php

declare(strict_types=1);

namespace App\Candidate\Enum;

enum Status: string
{
    case NEW = 'new';
    case SCREENED = 'screened';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public static function values(): array
    {
        return [
            self::NEW->value,
            self::SCREENED->value,
            self::APPROVED->value,
            self::REJECTED->value,
        ];
    }
}
