<?php

declare(strict_types=1);

namespace App\Ai\Operation\Dto;

use App\Ai\Operation\Enum\Status;

final readonly class Update
{
    public function __construct(
        public ?Status $status = null,
        public ?string $providerId = null,
        public ?array $rawResponse = null,
        public ?array $result = null,
    ) {
    }

    public function toArray(): array
    {
        $data = [];

        if ($this->status !== null) {
            $data['status'] = $this->status->value;
        }

        if ($this->providerId !== null) {
            $data['provider_id'] = $this->providerId;
        }

        if ($this->rawResponse !== null) {
            $data['raw_response'] = $this->rawResponse;
        }

        if ($this->result !== null) {
            $data['result'] = $this->result;
        }

        return $data;
    }
}
