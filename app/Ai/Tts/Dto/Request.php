<?php

declare(strict_types=1);

namespace App\Ai\Tts\Dto;

final readonly class Request
{
    public function __construct(
        public string $text,
        public string $voice = 'filipp',
        public float $speed = 1.0,
        public string $audioEncoding = 'OGG_OPUS'
    ) {
    }
}
