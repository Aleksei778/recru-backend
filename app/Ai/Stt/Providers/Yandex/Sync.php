<?php

declare(strict_types=1);

namespace App\Ai\Stt\Providers\Yandex;

use App\Ai\Stt\Providers\SttInterface;

final readonly class Sync extends Common implements SttInterface
{
    private const string SYNC_URL  = 'https://stt.api.cloud.yandex.net/stt/v3/recognize';

    public function recognize(string $filePath, string $format = 'OGG_OPUS'): ?string
    {
        $uri = $this->uploadAndGetUri($filePath);
        if (!$uri) return null;

        $response = $this->sendRequest(self::SYNC_URL, $uri, $format);
        if (!$response->successful()) {
            $this->logger->error('SpeechKit sync failed', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);
            return null;
        }

        return $this->extractText($response->json()['chunks'] ?? []);
    }
}
