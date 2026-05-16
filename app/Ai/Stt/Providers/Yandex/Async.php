<?php

declare(strict_types=1);

namespace App\Ai\Stt\Providers\Yandex;

use App\Ai\Operation\Models\Operation;
use App\Ai\Stt\Providers\SttInterface;
use App\Common\Enum\Locale;
use Illuminate\Http\Client\Response;

final readonly class Async extends Common implements SttInterface
{
    private const string ASYNC_URL = 'https://stt.api.cloud.yandex.net/stt/v3/recognizeFileAsync';
    private const string GET_RECOGNITION_URL = 'https://stt.api.cloud.yandex.net/stt/v3/getRecognition';

    public function recognize(
        string $audioPath,
        string $format = 'OGG_OPUS',
        Locale $locale = Locale::RU
    ): ?string {
        $url = $this->storage->url(
            disk: config('filesystems.default'),
            path: $audioPath,
        );

        $response = $this->sendRequest($url, $format, $locale);

        if (!$response->successful()) {
            $this->logger->error('SpeechKit async submit failed', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);
            return null;
        }

        return $response->json('id');
    }

    public function getResult(Operation $operation): ?string
    {
        try {
            $response = $this->client
                ->timeout(60)
                ->withHeaders([
                    'Authorization' => "Api-Key $this->apiKey",
                ])
                ->get(self::GET_RECOGNITION_URL, [
                    'operationId' => $operation->provider_id,
                ]);

            if (!$response->successful()) {
                $this->logger->error('SpeechKit getRecognition failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            return $this->parseRecognitionStream($response->body());
        } catch (\Exception $e) {
            $this->logger->error('Exception in SpeechKit getRecognition', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    private function parseRecognitionStream(string $body): string
    {
        $textParts = [];

        foreach (explode("\n", trim($body)) as $line) {
            $line = trim($line);
            if ($line === '') continue;

            $json = json_decode($line, true);
            if (!is_array($json)) continue;

            $alternatives = $json['result']['finalRefinement']['normalizedText']['alternatives'] ?? null;

            if ($alternatives && !empty($alternatives[0]['text'])) {
                $textParts[] = $alternatives[0]['text'];
            }
        }

        return trim(implode(' ', $textParts));
    }

    private function sendRequest(
        string $uri,
        string $format,
        Locale $locale = Locale::RU
    ): Response {
        $localeLower = strtolower($locale->value);
        $localeUpper = strtoupper($locale->value);

        return $this->client
            ->timeout(60)
            ->withHeaders([
                'Authorization' => "Api-Key $this->apiKey",
                'x-folder-id' => $this->folderId,
            ])
            ->post(self::ASYNC_URL, [
                'uri' => $uri,
                'recognitionModel' => [
                    'model' => 'general:rc',
                    'audioFormat' => [
                        'containerAudio' => ['containerAudioType' => $format],
                    ],
                    'textNormalization' => [
                        'textNormalization' => 'TEXT_NORMALIZATION_ENABLED',
                        'profanityFilter' => false,
                        'literatureText' => true,
                    ],
                    'languageRestriction' => [
                        'restrictionType' => 'WHITELIST',
                        'languageCode' => ["$localeLower-$localeUpper"],
                    ],
                ],
            ]);
    }
}
