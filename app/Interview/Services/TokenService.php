<?php

declare(strict_types=1);

namespace App\Interview\Services;

use App\Interview\Models\Interview;

final readonly class TokenService
{
    public function getInterviewPageUrl(Interview $interview): string
    {
        return config('app.url') . "/interviews/$interview->token/start";
    }

    public function generateToken(): string
    {
        return bin2hex(random_bytes(16));
    }
}
