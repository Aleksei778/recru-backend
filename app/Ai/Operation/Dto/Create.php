<?php

declare(strict_types=1);

namespace App\Ai\Operation\Dto;

use App\Ai\Operation\Enum\{Status, Type};

final readonly class Create
{
    public function __construct(
        public Type $type,
        public int $subjectId,
        public string $subjectType,
        public string $provider,
        public string $providerId,
        public Status $status = Status::Pending,
        public ?array $rawRequest = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type->value,
            'subject_id' => $this->subjectId,
            'subject_type' => $this->subjectType,
            'provider' => $this->provider,
            'provider_id' => $this->providerId,
            'status' => $this->status->value,
            'raw_request' => $this->rawRequest,
        ];
    }
}
