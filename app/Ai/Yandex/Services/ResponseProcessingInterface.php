<?php

declare(strict_types=1);

namespace App\Ai\Yandex\Services;

interface ResponseProcessingInterface
{
    public function processResponse(array $response): string;
}
