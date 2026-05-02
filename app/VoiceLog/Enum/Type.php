<?php

declare(strict_types=1);

namespace App\VoiceLog\Enum;

enum Type: string
{
    case Stt = 'stt';
    case Tts = 'tts';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
