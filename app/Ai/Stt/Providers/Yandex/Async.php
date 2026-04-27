<?php

declare(strict_types=1);

namespace App\Ai\Stt\Providers\Yandex;

use App\Ai\Stt\Providers\SttInterface;

final readonly class Async extends Common implements SttInterface
{
    private const string ASYNC_URL = 'https://stt.api.cloud.yandex.net/stt/v3/recognizeFileAsync';

    public function recognize(string $filePath, string $format = 'OGG_OPUS'): ?string
    {
        $uri = $this->uploadAndGetUri($filePath);
        if (!$uri) return null;

        $response = $this->sendRequest(self::ASYNC_URL, $uri, $format);
        if (!$response->successful()) {
            $this->logger->error('SpeechKit async submit failed', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);
            return null;
        }

        return $response->json('id');
    }
}
