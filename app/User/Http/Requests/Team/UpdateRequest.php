<?php

declare(strict_types=1);

namespace App\User\Http\Requests\Team;

use App\Common\Enum\Locale;
use App\User\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;

final class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var User $member */
        $member = $this->route('team');

        return [
            'email' => ['required', 'email', "unique:users,email,{$member->id}"],
            'name' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'locale' => ['required', new Enum(Locale::class)],
        ];
    }
}
