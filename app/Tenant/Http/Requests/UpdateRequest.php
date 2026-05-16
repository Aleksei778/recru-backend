<?php

declare(strict_types=1);

namespace App\Tenant\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'website' => ['sometimes', 'string', 'max:255'],
            'name' => ['sometimes', 'string', 'max:255'],
            'industry' => ['sometimes', 'string', 'max:255'],
        ];
    }
}
