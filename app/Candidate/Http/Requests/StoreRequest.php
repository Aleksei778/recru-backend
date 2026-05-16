<?php

declare(strict_types=1);

namespace App\Candidate\Http\Requests;

use App\Common\Enum\Locale;
use App\Candidate\Enum\{EducationLevel, Grade, Source, Status};
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

final class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:candidates,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'workplaces' => ['required', 'array'],
            'workplaces.*.position' => ['required', 'string', 'max:255'],
            'workplaces.*.company_name' => ['required', 'string', 'max:255'],
            'workplaces.*.description' => ['nullable', 'string', 'max:255'],
            'workplaces.*.started_at' => ['required', 'date'],
            'workplaces.*.end_at' => ['nullable', 'date'],
            'socials' => ['required', 'array'],
            'grade' => ['required', new Enum(Grade::class)],
            'socials.*.name' => ['required', 'string', 'max:255'],
            'socials.*.url' => ['required', 'url'],
            'source' => ['required', new Enum(Source::class)],
            'locale' => ['required', new Enum(Locale::class)],
            'status' => ['nullable', new Enum(Status::class)],
            'experience_years' => ['required', 'numeric'],
            'education_level' => ['required', new Enum(EducationLevel::class)],
            'skill_ids' => ['nullable', 'array'],
            'skill_ids.*' => ['integer', 'exists:skills,id'],
        ];
    }
}
