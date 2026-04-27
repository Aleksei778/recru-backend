<?php

declare(strict_types=1);

namespace App\Interview\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateQuestionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'questions'          => ['required', 'array', 'min:1'],
            'questions.*.id'     => ['required', 'integer', 'exists:questions,id'],
            'questions.*.text'   => ['required', 'string'],
            'questions.*.number' => ['required', 'integer', 'min:1'],
        ];
    }
}
