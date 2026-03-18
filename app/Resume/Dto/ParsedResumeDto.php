<?php

namespace App\Resume\Dto;

final readonly class ParsedResumeDto
{
    public function __construct(
        public string $first_name,
        public string $last_name,
        public ?string $middle_name,
        public ?string $email,
        public ?string $phone,
        public ?string $linkedin_url,
        public ?string $github_url,
        public ?int $experience_years,
        public ?string $grade,
        public ?string $education_level,
        public array $skills,
        public ?string $summary,
    ) {}

    public function toArray(): array
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'middle_name' => $this->middle_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'linkedin_url' => $this->linkedin_url,
            'github_url' => $this->github_url,
            'experience_years' => $this->experience_years,
            'grade' => $this->grade,
            'education_level' => $this->education_level,
            'skills' => $this->skills,
            'summary' => $this->summary,
        ];
    }
}
