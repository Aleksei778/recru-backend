<?php

declare(strict_types=1);

namespace App\Ai\Stt\Providers;

use App\Common\Enum\Locale;

interface SttInterface
{
    public function recognize(string $audioPath, string $format = 'OGG_OPUS'): ?string;
}
