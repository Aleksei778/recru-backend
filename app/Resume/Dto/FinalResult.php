<?php

declare(strict_types=1);

namespace App\Resume\Dto;

final readonly class FinalResult
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public ?string $middleName,
        public ?string $email,
        public ?string $phone,
        public array $socials,
        public ?int $experienceYears,
        public array $workPlaces,
        public ?string $educationLevel,
        public array $skills,
        public string $textGrade,
        public int $grade,
    ) {
    }

    public function toArray(): array
    {
        return [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'middle_name' => $this->middleName,
            'email' => $this->email,
            'phone' => $this->phone,
            'socials' => $this->socials,
            'experience_years' => $this->experienceYears,
            'workplaces' => $this->workPlaces,
            'education_level' => $this->educationLevel,
            'skills' => $this->skills,
            'text_grade' => $this->textGrade,
            'grade' => $this->grade,
        ];
    }
}
