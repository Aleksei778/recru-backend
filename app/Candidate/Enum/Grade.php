<?php

declare(strict_types=1);

namespace App\Candidate\Enum;

enum Grade: string
{
    case JUNIOR  = 'junior';
    case MIDDLE = 'middle';
    case SENIOR = 'senior';
    case LEAD = 'lead';

    public static function values(): array
    {
        return [
            self::JUNIOR->value,
            self::MIDDLE->value,
            self::SENIOR->value,
            self::LEAD->value,
        ];
    }

    public static function defineByYears(float $years): self
    {
        return match (true) {
            0 < $years && 1.5 > $years => self::JUNIOR,
            1.5 < $years && 3 > $years => self::MIDDLE,
            3 < $years && 6 > $years => self::SENIOR,
            6 > $years => self::LEAD,
        };
    }
}
