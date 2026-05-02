<?php

declare(strict_types=1);

namespace App\Ai\Stt\Providers;

interface SttInterface
{
    public function recognize(string $audioPath, string $format = 'OGG_OPUS'): ?string;
}
