<?php

declare(strict_types=1);

namespace App\Candidate\Http\Requests;

use App\Candidate\Enum\Source;
use Illuminate\Foundation\Http\FormRequest;
use App\Candidate\Enum\SeniorityLevel;

final class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'tenant_id' => 'required|exists:tenants,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:candidates,email',
            'phone' => 'nullable|string|max:20',
            'resume_url' => 'required|url',
            'linkedin_url' => 'nullable|url',
            'github_url' => 'nullable|url',
            'source' => 'required|in:' . implode(',', Source::values()),
            'experience_years' => 'required|integer|min:0|max:15',
            'education_level' => 'required|in:' . implode(',', SeniorityLevel::values()),
            'added_by_id' => 'required|exists:users,id',
        ];
    }
}
