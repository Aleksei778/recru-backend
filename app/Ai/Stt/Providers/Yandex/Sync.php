<?php

declare(strict_types=1);

namespace App\Ai\Stt\Providers\Yandex;

use App\Ai\Stt\Providers\SttInterface;
use App\Common\Enum\Locale;

final readonly class Sync extends Common implements SttInterface
{
    private const string SYNC_URL  = 'https://stt.api.cloud.yandex.net/stt/v3/recognize';

    public function recognize(string $audioPath, string $format = 'OGG_OPUS'): ?string
    {
        $audioContent = $this->storage->get(
            disk: config('filesystems.default'),
            path: $audioPath
        );

        if ($audioContent === null) {
            $this->logger->error('SpeechKit sync: cannot read audio file', [
                'path' => $audioPath,
            ]);
            return null;
        }

        $response = $this->client
            ->timeout(60)
            ->withHeaders([
                'Authorization' => "Api-Key $this->apiKey",
            ])
            ->withBody($audioContent, 'application/octet-stream')
            ->post(self::SYNC_URL . '?' . http_build_query([
                    'folderId' => $this->folderId,
                    'lang' => "ru-RU",
                    'format' => match ($format) {
                        'OGG_OPUS' => 'oggopus',
                        'MP3' => 'mp3',
                        default => 'lpcm',
                    },
                ]));

        if (!$response->successful()) {
            $this->logger->error('SpeechKit sync failed', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);
            return null;
        }

        return $response->json('result');
    }
}
