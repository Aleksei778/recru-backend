<?php

declare(strict_types=1);

namespace App\Candidate\Dto\Workplace;

use Carbon\{Carbon, CarbonInterface};

final readonly class Create
{
    public function __construct(
        public int $candidateId,
        public string $companyName,
        public string $position,
        public CarbonInterface $startedAt,
        public ?CarbonInterface $endedAt,
        public ?string $description,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            candidateId: (int) $data['candidate_id'],
            companyName: $data['company_name'],
            position: $data['position'],
            startedAt: Carbon::parse($data['started_at'] ?? null),
            endedAt: Carbon::parse($data['ended_at'] ?? null),
            description: $data['description'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'candidate_id' => $this->candidateId,
            'company_name' => $this->companyName,
            'position' => $this->position,
            'started_at' => $this->startedAt,
            'ended_at' => $this->endedAt,
            'description' => $this->description,
        ];
    }
}
