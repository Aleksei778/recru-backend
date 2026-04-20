<?php

declare(strict_types=1);

namespace App\Candidate\Dto\Candidate;

use App\Candidate\Enum\{EducationLevel, Source};

final readonly class Create
{
    public function __construct(
        public int $tenantId,
        public string $firstName,
        public string $lastName,
        public ?string $middleName,
        public string $email,
        public ?string $phone,
        public Source $source,
        public EducationLevel $educationLevel,
        public ?int $addedById = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            tenantId: (int) $data['tenant_id'],
            firstName: $data['first_name'],
            lastName: $data['last_name'],
            middleName: $data['middle_name'] ?? null,
            email: $data['email'],
            phone: $data['phone'] ?? null,
            source: $data['source'] instanceof Source ? $data['source'] : Source::from($data['source']),
            educationLevel: $data['education_level'] instanceof EducationLevel ? $data['education_level'] : EducationLevel::from($data['education_level']),
            addedById: $data['added_by_id'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'tenant_id' => $this->tenantId,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'middle_name' => $this->middleName,
            'email' => $this->email,
            'phone' => $this->phone,
            'source' => $this->source->value,
            'education_level' => $this->educationLevel->value,
            'added_by_id' => $this->addedById,
        ];
    }
}
