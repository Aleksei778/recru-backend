<?php

declare (strict_types=1);

namespace App\Ai\Yandex\Services\Speechkit;

use App\Ai\Yandex\Services\ObjectStorageService;
use App\Ai\Yandex\Services\Operation\ManageService as OperationService;
use Psr\Log\LoggerInterface;
use Illuminate\Http\Client\Factory as HttpClient;

final readonly class SttService
{
    private string $folderId;
    private string $apiKey;

    private const string STT_ASYNC_URL = 'https://stt.api.cloud.yandex.net/stt/v3/recognizeFileAsync';

    public function __construct(
        private LoggerInterface $logger,
        private HttpClient $client,
        private ObjectStorageService $storageService,
        private OperationService $operationService,
    ) {
        $this->folderId = config('services.yandex.folder_id');
        $this->apiKey = config('services.yandex.speechkit.api_secret');
    }

    public function getResult(string $operationId): ?string
    {
        $operation = $this->operationService->get($operationId);

        if (!$operation) {
            return null;
        }

        if (!($operation['done'] ?? false)) {
            return null;
        }

        $chunks = $operation['response']['chunks'] ?? [];
        $text = '';

        foreach ($chunks as $chunk) {
            $alternatives = $chunk['alternatives'] ?? [];
            if (!empty($alternatives)) {
                $text .= $alternatives[0]['text'] . ' ';
            }
        }

        return trim($text) ?: null;
    }

    public function send(string $objectStorageKey): ?string
    {
        try {
            $fileUri = $this->storageService
                ->getObjectUri($objectStorageKey);

            $this->logger->info('Sending audio to SpeechKit STT v3', [
                'file_uri' => $fileUri,
            ]);

            $payload = [
                'uri' => $fileUri,
                'recognitionModel' => [
                    'model' => 'general',
                    'audioFormat' => [
                        'containerAudio' => [
                            'containerAudioType' => 'OGG_OPUS',
                        ],
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
            ];

            $response = $this->client
                ->timeout(60)
                ->withHeaders([
                    'Authorization' => "Api-Key $this->apiKey",
                    'x-folder-id' => $this->folderId,
                ])
                ->post(self::STT_ASYNC_URL, $payload);

            $data = $response->json();

            if (!$response->successful()) {
                $this->logger->error('Speechkit STT v3 returned an error', [
                    'status' => $response->status(),
                    'body' => $data,
                ]);

                return null;
            }

            $operationId = $data['id'] ?? null;

            if (!$operationId) {
                $this->logger->error('Speechkit STT v3 response missing operation id', [
                    'body' => $data,
                ]);

                return null;
            }

            $this->logger->info('SpeechKit STT v3 operation created.', [
                'operation_id' => $operationId,
            ]);

            return $operationId;
        } catch (\Exception $e) {
            $this->logger->error('Exception in SpeechKit STT v3.', [
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
