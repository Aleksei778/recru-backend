<?php

declare(strict_types=1);

namespace App\Candidate\Dto\Social;

final readonly class Create
{
    public function __construct(
        public int $candidateId,
        public string $name,
        public string $url,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            candidateId: (int) $data['candidate_id'],
            name: $data['name'],
            url: $data['url'],
        );
    }

    public function toArray(): array
    {
        return [
            'candidate_id' => $this->candidateId,
            'name' => $this->name,
            'url' => $this->url,
        ];
    }
}
