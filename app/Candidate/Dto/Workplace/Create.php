<?php

declare(strict_types=1);

namespace App\Candidate\Dto\Workplace;

use Carbon\{Carbon, CarbonInterface};

final readonly class Create
{
    public function __construct(
        public int $candidate_id,
        public string $company_name,
        public string $position,
        public CarbonInterface $started_at,
        public ?CarbonInterface $ended_at,
        public ?string $description,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            candidate_id: (int) $data['candidate_id'],
            company_name: $data['company_name'],
            position: $data['position'],
            started_at: Carbon::parse($data['started_at'] ?? null),
            ended_at: Carbon::parse($data['ended_at'] ?? null),
            description: $data['description'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'candidate_id' => $this->candidate_id,
            'company_name' => $this->company_name,
            'position' => $this->position,
            'started_at' => $this->started_at,
            'ended_at' => $this->ended_at,
            'description' => $this->description,
        ];
    }
}
