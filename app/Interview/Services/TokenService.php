<?php

declare(strict_types=1);

namespace App\Interview\Services;

use App\Interview\Models\Interview;
use Illuminate\Support\Str;

final readonly class TokenService
{
    public function getInterviewPageUrl(Interview $interview): string
    {
        return config('app.url') . "/{$interview->candidate->locale->value}/interview/$interview->token";
    }

    public function generateToken(): string
    {
        return Str::random(32);
    }
}
