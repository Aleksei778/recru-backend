<?php

namespace App\Resume\Http\Requests;

use App\Common\Enum\Locale;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

final class FileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'resume' => [
                'required',
                'file',
                'mimes:pdf,txt',
                'max:5120',
            ],
            'locale' => [
                'required',
                new Enum(Locale::class),
            ],
        ];
    }
}
