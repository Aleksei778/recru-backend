<?php

declare(strict_types=1);

namespace App\Ai\Stt\Contracts;

use App\Ai\Stt\Enum\Provider;
use App\Ai\Operation\Models\Operation;

interface AsyncInterface
{
    public function recognizeAsync(string $filePath, string $format = 'OGG_OPUS'): ?Operation;
    public function getRecognitionResult(Operation $operation): ?string;
}
