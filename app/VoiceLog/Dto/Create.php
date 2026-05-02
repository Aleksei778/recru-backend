<?php

declare(strict_types=1);

namespace App\VoiceLog\Dto;

use App\VoiceLog\Enum\Type;

final readonly class Create
{
    public function __construct(
        public int $subjectId,
        public string $subjectType,
        public string $audioPath,
        public string $mimeType,
        public Type $type,
    ) {
    }

    public function toArray(): array
    {
        return [
            'subject_id' => $this->subjectId,
            'subject_type' => $this->subjectType,
            'audio_path' => $this->audioPath,
            'mimetype' => $this->mimeType,
            'type' => $this->type,
        ];
    }
}
