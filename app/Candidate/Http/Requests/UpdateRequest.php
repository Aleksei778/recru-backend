<?php

declare(strict_types=1);

namespace App\Candidate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Candidate\Enum\Status;
use App\Candidate\Enum\EducationLevel;

final class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'middle_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:candidates,email',
            'phone' => 'sometimes|string|max:20',
            'resume_url' => 'sometimes|url',
            'linkedin_url' => 'sometimes|url',
            'github_url' => 'sometimes|url',
            'status' => 'sometimes|in:' . implode(',', Status::values()),
            'experience_years' => 'sometimes|integer|min:0|max:15',
            'education_level' => 'sometimes|in:' . implode(',', EducationLevel::values()),
        ];
    }
}
