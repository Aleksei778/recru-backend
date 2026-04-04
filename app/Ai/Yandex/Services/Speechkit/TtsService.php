<?php

declare(strict_types=1);

namespace App\Ai\Yandex\Services\Speechkit;

use App\Ai\Yandex\Dto\Tts\{Request, Result};
use Psr\Log\LoggerInterface;
use Illuminate\Http\Client\Factory as HttpClient;

final readonly class TtsService
{
    private string $folderId;
    private string $apiKey;

    private const TTS_URL = 'https://tts.api.cloud.yandex.net/api/v3/utteranceSynthesis';
    private const AUDIO_FORMAT_MAP = [
        'OGG_OPUS' => ['mime' => 'audio/ogg', 'type' => 'OGG_OPUS'],
        'MP3' => ['mime' => 'audio/mpeg', 'type' => 'MP3'],
        'WAV' => ['mime' => 'audio/wav', 'type' => 'WAV'],
    ];

    public function __construct(
        private LoggerInterface $logger,
        private HttpClient $client
    ) {
        $this->folderId = config('services.yandex.folder_id');
        $this->apiKey = config('services.yandex.speechkit.api_secret');
    }

    public function synthesize(Request $request): ?Result
    {
        $format = self::AUDIO_FORMAT_MAP[$request->audioEncoding]
            ?? self::AUDIO_FORMAT_MAP['OGG_OPUS'];

        try {
            $payload = [
                'text' => $request->text,
                'outputAudioSpec' => [
                    'containerAudio' => [
                        'containerAudioType' => $format['type'],
                    ],
                ],
                'hints' => [
                    ['voice' => $request->voice],
                    ['speed' => $request->speed],
                ],
                'loudnessNormalizationOptions' => [
                    'minLoudness' => -24.0,
                    'maxLoudness' => -6.0,
                ],
            ];

            $response = $this->client
                ->timeout(seconds: 60)
                ->withHeaders([
                    'Authorization' => "Api-Key $this->apiKey",
                    'x-folder-id' => $this->folderId,
                ])
                ->post(self::TTS_URL, $payload);

            if (!$response->successful()) {
                $this->logger->error('Speechkit TTS v3 returned an error', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);

                return null;
            }

            $audioContent = $this->decodeChunkedBody($response->body());

            if ($audioContent === '') {
                $this->logger->error('Speechkit TTS returned an empty response');

                return null;
            }

            return new Result(
                audioContent: $audioContent,
                mimeType: $format['mimetype']
            );
        } catch (\Exception $e) {
            $this->logger->error('Exception in Speechkit TTS v3', [
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function decodeChunkedBody(string $body): string
    {
        $binary = '';

        foreach (explode("\n", trim($body)) as $line) {
            $line = trim($line);

            if ($line === '') {
                continue;
            }

            $json = json_decode($line, true);

            $chunk = $json['result']['audioChunk']['data'] ?? null;

            if ($chunk == null) {
                $this->logger->warning('Speechkit TTS v3 returned no chunk shape', [
                    'line' => $line,
                ]);

                continue;
            }

            $binary = base64_decode($chunk);
        }

        return $binary;
    }
}
