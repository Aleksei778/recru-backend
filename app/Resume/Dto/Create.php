<?php

declare(strict_types=1);

namespace App\Resume\Dto;

final readonly class Create
{
    public function __construct(
        public string $filePath,
        public string $mimetype,
    ) {
    }

    public function toArray(): array
    {
        return [
            'file_path' => $this->filePath,
            'mimetype' => $this->mimetype,
        ];
    }
}
