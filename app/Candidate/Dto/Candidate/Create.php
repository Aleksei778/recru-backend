<?php

declare(strict_types=1);

namespace App\Candidate\Dto\Candidate;

use App\Common\Enum\Locale;
use App\Candidate\Enum\{EducationLevel, Source};

final readonly class Create
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public ?string $middleName,
        public string $email,
        public ?string $phone,
        public Source $source,
        public float $experienceYears,
        public EducationLevel $educationLevel,
        public Locale $locale,
        public ?int $addedById = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            firstName: $data['first_name'],
            lastName: $data['last_name'],
            middleName: $data['middle_name'] ?? null,
            email: $data['email'],
            phone: $data['phone'] ?? null,
            source: $data['source'] instanceof Source
                ? $data['source']
                : Source::from($data['source']),
            experienceYears: (float) $data['experience_years'],
            educationLevel: $data['education_level'] instanceof EducationLevel
                ? $data['education_level']
                : EducationLevel::from($data['education_level']),
            locale: $data['locale'] instanceof Locale
                ? $data['locale']
                : Locale::from($data['locale']),
            addedById: (int) $data['added_by_id'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'middle_name' => $this->middleName,
            'email' => $this->email,
            'phone' => $this->phone,
            'experience_years' => $this->experienceYears,
            'source' => $this->source->value,
            'education_level' => $this->educationLevel->value,
            'locale' => $this->locale->value,
            'added_by_id' => $this->addedById,
        ];
    }
}
