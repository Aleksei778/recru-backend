<?php

declare(strict_types=1);

namespace App\Candidate\Http\Requests;

use App\Candidate\Enum\{EducationLevel, Status};
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
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'middle_name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'unique:candidates,email,' . $this->candidate?->id],
            'phone' => ['sometimes', 'string', 'max:20'],
            'resume_url' => ['sometimes', 'url'],
            'linkedin_url' => ['sometimes', 'url'],
            'github_url' => ['sometimes', 'url'],
            'status' => ['sometimes', new Enum(Status::class)],
            'experience_years' => ['sometimes', 'integer', 'min:0', 'max:50'],
            'education_level' => ['sometimes', new Enum(EducationLevel::class)],
        ];
    }
}
