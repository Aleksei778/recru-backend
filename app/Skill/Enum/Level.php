<?php

namespace App\Skill\Enum;

enum Level: string
{
    case BEGINNER = 'beginner';
    case INTERMEDIATE = 'intermediate';
    case ADVANCED = 'advanced';

    public static function values(): array
    {
        return [
            self::BEGINNER->value,
            self::INTERMEDIATE->value,
            self::ADVANCED->value,
        ];
    }
}
