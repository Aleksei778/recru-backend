<?php

declare(strict_types=1);

namespace App\VoiceLog\Dto;

use App\VoiceLog\Enum\Status;

class Create
{
    public function __construct(
        public string $yandexId,
        public int $duration,
        public int $size,
        public string $mimeType,
        public Status $status = Status::New,
        public ?array $rawResponse = null,
    ) {
    }
}
