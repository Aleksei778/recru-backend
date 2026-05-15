<?php

declare(strict_types=1);

namespace App\Vacancy\Dto;

use App\Candidate\Enum\{EducationLevel, Grade};
use App\Vacancy\Enum\{
    EmploymentType,
    Status,
    WorkMode
};

final readonly class Create
{
    public function __construct(
        public string $title,
        public string $description,
        public EmploymentType $employmentType,
        public WorkMode $workMode,
        public float $experienceYears,
        public Grade $grade,
        public ?int $salaryMin = null,
        public ?int $salaryMax = null,
        public ?string $salaryCurrency = null,
        public Status $status,
        public ?string $location = null,
        public ?EducationLevel $educationLevel = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            description: $data['description'],
            employmentType: EmploymentType::from($data['employment_type']),
            workMode: WorkMode::from($data['work_mode']),
            experienceYears: (float) $data['experience_years'],
            grade: Grade::from($data['grade']),
            salaryMin: isset($data['salary_min']) ? (int) $data['salary_min'] : null,
            salaryMax: isset($data['salary_max']) ? (int) $data['salary_max'] : null,
            salaryCurrency: $data['salary_currency'] ?? null,
            status: isset($data['status']) ? Status::from($data['status']) : null,
            location: $data['location'] ?? null,
            educationLevel: isset($data['education_level']) ? EducationLevel::from($data['education_level']) : null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'title' => $this->title,
            'description' => $this->description,
            'employment_type' => $this->employmentType,
            'work_mode' => $this->workMode,
            'experience_years' => $this->experienceYears,
            'grade' => $this->grade,
            'salary_min' => $this->salaryMin,
            'salary_max' => $this->salaryMax,
            'salary_currency' => $this->salaryCurrency,
            'status' => $this->status,
            'location' => $this->location,
            'education_level' => $this->educationLevel,
        ], fn($value) => $value !== null);
    }
}
