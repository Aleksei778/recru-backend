<?php

declare(strict_types=1);

namespace App\Ai\Stt\Providers;

use App\Ai\Operation\Models\Operation;
use App\Ai\Stt\Contracts\{AsyncInterface, SyncInterface};
use Illuminate\Http\Client\Factory as HttpClient;
use Illuminate\Support\Facades\Storage;
use Psr\Log\LoggerInterface;

final readonly class Yandex implements SyncInterface, AsyncInterface
{
    private const string SYNC_URL  = 'https://stt.api.cloud.yandex.net/stt/v3/recognize';
    private const string ASYNC_URL = 'https://stt.api.cloud.yandex.net/stt/v3/recognizeFileAsync';

    private string $folderId;
    private string $apiKey;
    private string $disk;

    public function __construct(
        private HttpClient $client,
        private LoggerInterface $logger,
    ) {
        $this->folderId = config('services.yandex.folder_id');
        $this->apiKey = config('services.yandex.api_key');
        $this->disk = config('services.yandex.storage.disk');
    }

    public function recognizeSync(string $filePath, string $format = 'OGG_OPUS'): ?string
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

    public function recognizeAsync(string $filePath, string $format = 'OGG_OPUS'): ?string
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

    public function getRecognitionResult(Operation $operation): ?string
    {
        $response = $this->client
            ->withHeaders([
                'Authorization' => "Api-Key $this->apiKey",
                'x-folder-id' => $this->folderId,
            ])
            ->get("https://operation.api.cloud.yandex.net/operations/$operation->provider_id");

        $data = $response->json();

        if (!($data['done'] ?? false)) {
            return null;
        }

        return $this->extractText($data['response']['chunks'] ?? []);
    }

    private function uploadAndGetUri(string $filePath): ?string
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

    private function sendRequest(string $url, string $uri, string $format): \Illuminate\Http\Client\Response
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

    private function extractText(array $chunks): string
    {
        return trim(implode(' ', array_map(
            fn($chunk) => $chunk['alternatives'][0]['text'] ?? '',
            $chunks
        )));
    }
}
