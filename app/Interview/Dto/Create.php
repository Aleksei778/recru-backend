<?php

declare(strict_types=1);

namespace App\Interview\Dto;

use App\Candidate\Models\Candidate;
use App\Vacancy\Models\Vacancy;
use Carbon\CarbonInterface;

final readonly class Create
{
    public function __construct(
        public Vacancy $vacancy,
        public Candidate $candidate,
        public string $token,
        public CarbonInterface $tokenExpiresAt,
        public ?string $additionalInfo,
    ) {
    }

    public function toArray(): array
    {
        return [
            'vacancy_id' => $this->vacancy->id,
            'candidate_id' => $this->candidate->id,
            'token' => $this->token,
            'token_expires_at' => $this->tokenExpiresAt,
            'additional_info' => $this->additionalInfo,
        ];
    }
}
