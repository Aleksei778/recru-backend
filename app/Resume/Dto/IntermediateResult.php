<?php

declare(strict_types=1);

namespace App\Resume\Dto;

final readonly class IntermediateResult
{
    public function __construct(
        public string $parseOperationId,
        public string $evaluateOperationId,
    ) {
    }

    public function toArray(): array
    {
        return [
            'parse_operation_id' => $this->parseOperationId,
            'evaluate_operation_id' => $this->evaluateOperationId,
        ];
    }
}
