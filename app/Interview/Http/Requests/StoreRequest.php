<?php

declare(strict_types=1);

namespace App\Interview\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Interview\Enum\Status;
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
            'candidate_id' => ['required', 'exists:candidates,id'],
            'vacancy_id' => ['required', 'exists:vacancies,id'],
            'questions_number' => ['required', 'integer', 'min:5'],
        ];
    }
}
