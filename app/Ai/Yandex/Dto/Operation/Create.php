<?php

declare(strict_types=1);

namespace App\Ai\Yandex\Dto\Operation;

use App\Ai\Yandex\Enum\OperationStatus;

final readonly class Create
{
    public function __construct(
        public int $interviewId,
        public ?string $yandexId = null,
        public OperationStatus $status = OperationStatus::NEW,
        public ?array $rawRequest = null,
        public ?array $rawResponse = null,
        public ?array $result = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'interview_id' => $this->interviewId,
            'yandex_id' => $this->yandexId,
            'status' => $this->status->value,
            'raw_request' => $this->rawRequest,
            'raw_response' => $this->rawResponse,
            'result' => $this->result,
        ];
    }
}
