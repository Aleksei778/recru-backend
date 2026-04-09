<?php

declare(strict_types=1);

namespace App\Interview\Services;

use App\Interview\Models\Interview;

final readonly class UrlService
{
    public function getInterviewPageUrl(Interview $interview): string
    {
        return config('app.url') . "/interviews/$interview->token/start";
    }
}
