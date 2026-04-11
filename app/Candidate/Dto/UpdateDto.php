<?php

declare(strict_types=1);

namespace App\Candidate\Dto;

use App\Candidate\Enum\{EducationLevel, Status};

final readonly class UpdateDto
{
    public function __construct(
        public ?string $first_name = null,
        public ?string $last_name = null,
        public ?string $middle_name = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $resume_url = null,
        public ?string $linkedin_url = null,
        public ?string $github_url = null,
        public ?Status $status = null,
        public ?int $experience_years = null,
        public ?EducationLevel $education_level = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            first_name: $data['first_name'] ?? null,
            last_name: $data['last_name'] ?? null,
            middle_name: $data['middle_name'] ?? null,
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? null,
            resume_url: $data['resume_url'] ?? null,
            linkedin_url: $data['linkedin_url'] ?? null,
            github_url: $data['github_url'] ?? null,
            status: isset($data['status']) ? (is_string($data['status']) ? Status::from($data['status']) : $data['status']) : null,
            experience_years: isset($data['experience_years']) ? (int) $data['experience_years'] : null,
            education_level: isset($data['education_level']) ? (is_string($data['education_level']) ? EducationLevel::from($data['education_level']) : $data['education_level']) : null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'middle_name' => $this->middle_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'resume_url' => $this->resume_url,
            'linkedin_url' => $this->linkedin_url,
            'github_url' => $this->github_url,
            'status' => $this->status?->value,
            'experience_years' => $this->experience_years,
            'education_level' => $this->education_level?->value,
        ], fn ($value) => $value !== null);
    }
}
