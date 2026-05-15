<?php

declare(strict_types=1);

namespace App\Vacancy\Dto;

use App\Candidate\Enum\{EducationLevel, Grade};
use App\Vacancy\Enum\{
    EmploymentType,
    Status,
    WorkMode
};

final readonly class Update
{
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?EmploymentType $employmentType = null,
        public ?WorkMode $workMode = null,
        public ?int $salaryMin = null,
        public ?int $salaryMax = null,
        public ?string $salaryCurrency = null,
        public ?Status $status = null,
        public ?string $location = null,
        public ?Grade $grade = null,
        public ?float $experienceYears = null,
        public ?EducationLevel $educationLevel = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'] ?? null,
            description: $data['description'] ?? null,
            employmentType: isset($data['employment_type']) ? EmploymentType::from($data['employment_type']) : null,
            workMode: isset($data['work_mode']) ? WorkMode::from($data['work_mode']) : null,
            salaryMin: isset($data['salary_min']) ? (int) $data['salary_min'] : null,
            salaryMax: isset($data['salary_max']) ? (int) $data['salary_max'] : null,
            salaryCurrency: $data['salary_currency'] ?? null,
            status: isset($data['status']) ? Status::from($data['status']) : null,
            location: $data['location'] ?? null,
            grade: isset($data['grade']) ? Grade::from($data['grade']) : null,
            experienceYears: isset($data['experience_years']) ? (float) $data['experience_years'] : null,
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
            'salary_min' => $this->salaryMin,
            'salary_max' => $this->salaryMax,
            'salary_currency' => $this->salaryCurrency,
            'status' => $this->status,
            'location' => $this->location,
            'grade' => $this->grade,
            'experience_years' => $this->experienceYears,
            'education_level' => $this->educationLevel,
        ], fn($value) => $value !== null);
    }

    public function isPublished(): bool
    {
        return $this->status === Status::PUBLISHED;
    }

    public function isClosed(): bool
    {
        return $this->status === Status::CLOSED;
    }
}
