<?php

declare(strict_types=1);

namespace App\Ai\Gpt\Providers\Yandex;

use App\Ai\Gpt\Providers\GptInterface;

final readonly class Sync extends Common implements GptInterface
{
    private const string SYNC_URL = 'https://llm.api.cloud.yandex.net/foundationModels/v1/completion';

    public function completion(array $messages, float $temperature = 0.3, int $maxTokens = 2000): ?string
    {
        $data = $this->sendRequest(self::SYNC_URL, $messages, $temperature, $maxTokens);

        return $data['result']['alternatives'][0]['message']['text'] ?? null;
    }
}
