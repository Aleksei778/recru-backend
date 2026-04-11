<?php

declare(strict_types=1);

namespace App\Candidate\Dto;

use App\Candidate\Enum\{EducationLevel, Source};

final readonly class CreateDto
{
    public function __construct(
        public int $tenant_id,
        public string $first_name,
        public string $last_name,
        public ?string $middle_name,
        public string $email,
        public ?string $phone,
        public string $resume_url,
        public ?string $linkedin_url,
        public ?string $github_url,
        public Source $source,
        public int $experience_years,
        public EducationLevel $education_level,
        public ?int $added_by_id = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            tenant_id: (int) $data['tenant_id'],
            first_name: $data['first_name'],
            last_name: $data['last_name'],
            middle_name: $data['middle_name'] ?? null,
            email: $data['email'],
            phone: $data['phone'] ?? null,
            resume_url: $data['resume_url'],
            linkedin_url: $data['linkedin_url'] ?? null,
            github_url: $data['github_url'] ?? null,
            source: $data['source'] instanceof Source ? $data['source'] : Source::from($data['source']),
            experience_years: (int) $data['experience_years'],
            education_level: $data['education_level'] instanceof EducationLevel ? $data['education_level'] : EducationLevel::from($data['education_level']),
            added_by_id: $data['added_by_id'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'tenant_id' => $this->tenant_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'middle_name' => $this->middle_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'resume_url' => $this->resume_url,
            'linkedin_url' => $this->linkedin_url,
            'github_url' => $this->github_url,
            'source' => $this->source->value,
            'experience_years' => $this->experience_years,
            'education_level' => $this->education_level->value,
            'added_by_id' => $this->added_by_id,
        ];
    }
}
