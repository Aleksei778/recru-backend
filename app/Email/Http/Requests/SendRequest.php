<?php

declare(strict_types=1);

namespace App\Email\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class SendRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'interview_id' => 'required|integer|exists:interviews,id',
            'locale' => 'required|string|in:ru,en',
        ];
    }
}
