<?php

declare(strict_types=1);

namespace App\Ai\Yandex\Dto\Tts;

final readonly class Result
{
    public function __construct(
        public string $audioContent,
        public string $mimeType,
    ) {
    }
}
