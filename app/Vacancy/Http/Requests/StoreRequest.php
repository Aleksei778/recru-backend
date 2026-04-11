<?php

declare(strict_types=1);

namespace App\Vacancy\Http\Requests;

use App\Vacancy\Enum\EmploymentType;
use App\Vacancy\Enum\Status;
use App\Vacancy\Enum\WorkMode;
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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'employment_type' => ['required', new Enum(EmploymentType::class)],
            'work_mode' => ['required', new Enum(WorkMode::class)],
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'min:0'],
            'salary_currency' => ['nullable', 'string', 'max:10'],
            'status' => ['nullable', new Enum(Status::class)],
            'location' => ['nullable', 'string', 'max:255'],
        ];
    }
}
