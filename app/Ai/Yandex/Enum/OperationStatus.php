<?php

declare(strict_types=1);

namespace App\Ai\Yandex\Enum;

enum OperationStatus: string
{
    case NEW = 'new';
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case RESPONSE_RECEIVED = 'response_received';
    case RESPONSE_RECEIVED_FAILED = 'response_received_failed';
    case COMPLETED = 'completed';
    case FAILED = 'failed';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
