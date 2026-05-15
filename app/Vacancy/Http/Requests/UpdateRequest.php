<?php

declare(strict_types=1);

namespace App\Vacancy\Http\Requests;

use App\Candidate\Enum\EducationLevel;
use App\Candidate\Enum\Grade;
use App\Vacancy\Enum\{EmploymentType, Status, WorkMode};
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

final class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'employment_type' => ['sometimes', new Enum(EmploymentType::class)],
            'work_mode' => ['sometimes', new Enum(WorkMode::class)],
            'salary_min' => ['nullable', 'integer', 'min:0'],
            'salary_max' => ['nullable', 'integer', 'min:0'],
            'salary_currency' => ['nullable', 'string', 'max:10'],
            'status' => ['sometimes', new Enum(Status::class)],
            'location' => ['nullable', 'string', 'max:255'],
            'experience_years' => ['sometimes', 'numeric', 'min:0'],
            'grade' => ['sometimes', new Enum(Grade::class)],
            'education_level' => ['sometimes', new Enum(EducationLevel::class)],
            'skill_ids' => ['sometimes', 'array'],
            'skill_ids.*' => ['integer', 'exists:skills,id'],
        ];
    }
}
