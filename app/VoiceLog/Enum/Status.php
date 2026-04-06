<?php

declare(strict_types=1);

namespace App\VoiceLog\Enum;

enum Status: string
{
    case New = 'new';
    case Pending = 'pending';
    case ResponseReceived = 'response_received';
    case ResponseReceivedFailed = 'response_received_failed';
    case Failed = 'failed';
    case FailedMessage = 'failed_message';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
