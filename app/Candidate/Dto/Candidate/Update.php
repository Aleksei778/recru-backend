<?php

declare(strict_types=1);

namespace App\Candidate\Dto\Candidate;

use App\Common\Enum\Locale;
use App\Candidate\Enum\EducationLevel;

final readonly class Update
{
    public function __construct(
        public ?string $firstName = null,
        public ?string $lastName = null,
        public ?string $middleName = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?int $experienceYears = null,
        public ?EducationLevel $educationLevel = null,
        public ?Locale $locale,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            firstName: $data['first_name'] ?? null,
            lastName: $data['last_name'] ?? null,
            middleName: $data['middle_name'] ?? null,
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? null,
            experienceYears: isset($data['experience_years'])
                ? (int) $data['experience_years']
                : null,
            educationLevel: isset($data['education_level'])
                ? (is_string($data['education_level'])
                    ? EducationLevel::from($data['education_level'])
                    : $data['education_level'])
                : null,
            locale: $data['locale'] instanceof Locale
                ? $data['locale']
                : Locale::from($data['locale']),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'middle_name' => $this->middleName,
            'email' => $this->email,
            'phone' => $this->phone,
            'experience_years' => $this->experienceYears,
            'education_level' => $this->educationLevel?->value,
            'locale' => $this->locale?->value,
        ], fn ($value) => $value !== null);
    }
}
