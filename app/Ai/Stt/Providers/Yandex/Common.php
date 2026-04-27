<?php

declare(strict_types=1);

namespace App\Ai\Stt\Providers\Yandex;

use Illuminate\Http\Client\Factory as HttpClient;
use Illuminate\Support\Facades\Storage;
use Psr\Log\LoggerInterface;

readonly class Common
{
    protected string $folderId;
    protected string $apiKey;
    protected string $disk;

    public function __construct(
        protected HttpClient $client,
        protected LoggerInterface $logger,
    ) {
        $this->folderId = config('services.yandex.folder_id');
        $this->apiKey = config('services.yandex.api_key');
        $this->disk = config('services.yandex.storage.disk');
    }

    protected function uploadAndGetUri(string $filePath): ?string
    {
        try {
            $key = 'audio-temp/' . basename($filePath);
            Storage::disk($this->disk)->put($key, file_get_contents($filePath));

            return Storage::disk($this->disk)->url($key);
        } catch (\Exception $e) {
            $this->logger->error('Failed to upload audio to storage', [
                'path' => $filePath,
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    protected function sendRequest(string $url, string $uri, string $format): \Illuminate\Http\Client\Response
    {
        return $this->client
            ->timeout(60)
            ->withHeaders([
                'Authorization' => "Api-Key $this->apiKey",
                'x-folder-id' => $this->folderId,
            ])
            ->post($url, [
                'uri' => $uri,
                'recognitionModel' => [
                    'model' => 'general',
                    'audioFormat' => [
                        'containerAudio' => ['containerAudioType' => $format],
                    ],
                    'textNormalization' => [
                        'textNormalization' => 'TEXT_NORMALIZATION_ENABLED',
                        'profanityFilter' => false,
                        'literatureText' => false,
                    ],
                    'languageRestriction' => [
                        'restrictionType' => 'WHITELIST',
                        'languageCode' => ['ru-RU'],
                    ],
                ],
            ]);
    }

    protected function extractText(array $chunks): string
    {
        return trim(implode(' ', array_map(
            fn($chunk) => $chunk['alternatives'][0]['text'] ?? '',
            $chunks
        )));
    }
}
