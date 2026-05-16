<?php

declare(strict_types=1);

namespace App\User\Http\Requests\Profile;

use App\Common\Enum\Locale;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

final class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'unique:users,email'],
            'name' => ['nullable', 'string', 'max:255'],
            'locale' => ['required', new Enum(Locale::class)],
        ];
    }
}
