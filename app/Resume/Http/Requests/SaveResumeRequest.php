<?php

declare(strict_types=1);

namespace App\Resume\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class SaveResumeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mode' => ['required', 'in:new,existing'],
            'candidate_id' => ['required_if:mode,existing', 'exists:candidates,id'],
            'resume_id' => ['required', 'exists:resumes,id'],
        ];
    }
}
