<?php

declare(strict_types=1);

namespace App\Ai\Tts\Dto;

final readonly class Result
{
    public function __construct(
        public string $audioContent,
        public string $mimeType,
    ) {
    }
}
