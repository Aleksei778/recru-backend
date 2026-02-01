<?php

declare(strict_types=1);

namespace App\Tenant\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

final class LoginRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'domain' => 'required|string|exists:domains',
        ];
    }
}
