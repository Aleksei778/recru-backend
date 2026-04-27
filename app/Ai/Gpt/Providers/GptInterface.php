<?php

declare(strict_types=1);

namespace App\Ai\Gpt\Providers;

use App\Ai\Gpt\Dto\Message;

interface GptInterface
{
    /**
     * @param Message[] $messages
     * @param float $temperature
     * @param int $maxTokens
     */
    public function completion(array $messages, float $temperature = 0.3, int $maxTokens = 2000): ?string;
}
