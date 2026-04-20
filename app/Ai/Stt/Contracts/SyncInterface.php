<?php

declare(strict_types=1);

namespace App\Ai\Stt\Contracts;

interface SyncInterface
{
    public function recognizeSync(string $filePath, string $format = 'OGG_OPUS'): ?string;
}
