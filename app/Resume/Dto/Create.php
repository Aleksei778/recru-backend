<?php

declare(strict_types=1);

namespace App\Resume\Dto;

final readonly class Create
{
    public function __construct(
        public string $filePath,
        public string $fileName,
        public string $mimeType,
        public int $size,
        public string $storageDisk,
        public int $savedById,
    ) {
    }

    public function toArray(): array
    {
        return [
            'file_path' => $this->filePath,
            'file_name' => $this->fileName,
            'mime_type' => $this->mimeType,
            'size' => $this->size,
            'storage_disk' => $this->storageDisk,
            'saved_by_id' => $this->savedById,
        ];
    }
}
