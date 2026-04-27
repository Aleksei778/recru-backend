<?php

declare(strict_types=1);

namespace App\Interview\Http\Requests;

use App\Interview\Enum\Decision;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

final class CloseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'decision' => ['required', new Enum(Decision::class)],
        ];
    }
}
