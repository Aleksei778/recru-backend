<?php

declare(strict_types=1);

namespace App\Ai\Yandex\Dto;

final readonly class ObjectStorage
{
    public function __construct(
        public string $content,
        public string $mimeType,
        public string $fileId,
        public string $fileSize,
    ) {
    }
}
