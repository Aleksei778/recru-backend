<?php

declare(strict_types=1);

namespace App\Ai\Yandex\Dto\Operation;

use App\Ai\Yandex\Enum\OperationStatus;

final readonly class Update
{
    public function __construct(
        public int $id,
        public OperationStatus $status,
        public ?int $tryCount = null,
        public ?array $rawResponse = null,
        public ?array $result = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'tryCount' => $this->tryCount,
            'rawResponse' => $this->rawResponse,
            'result' => $this->result,
        ];
    }
}
