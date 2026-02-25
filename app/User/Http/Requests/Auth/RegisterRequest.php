<?php

declare(strict_types=1);

namespace App\Common\Http\Requests\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Psr\Log\LoggerInterface;

final class RegisterRequest extends FormRequest
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
        parent::__construct();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'company' => [
                'required',
                'string',
                'max:255'
            ],
            'subdomain' => [
                'required',
                'string',
                'max:63',
                'unique:domains,domain',
            ],
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255',
            ],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols()
            ],
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator): void
    {
        $this->logger->error('Validation failed for DomainRegisterRequest', [
            'errors' => $validator->errors()->toArray(),
            'input' => $this->all(),
            'url' => $this->fullUrl(),
            'ip' => $this->ip(),
            'user_agent' => $this->userAgent()
        ]);

        parent::failedValidation($validator);
    }
}
