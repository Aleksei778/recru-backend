<?php

declare(strict_types=1);

namespace App\Email\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class SendRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'candidate_id' => 'required|integer|exists:candidates,id',
            'vacancy_id' => 'required|integer|exists:vacancies,id',
        ];
    }
}
