<?php

namespace App\Resume\Http\Requests;

use App\Common\Enum\Locale;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

final class StringRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'text' => [
                'required',
                'string',
                'min:50',
                'max:20000',
            ],
            'locale' => [
                'required',
                new Enum(Locale::class),
            ],
        ];
    }
}
