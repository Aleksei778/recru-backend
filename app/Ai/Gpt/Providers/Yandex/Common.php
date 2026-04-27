<?php

declare(strict_types=1);

namespace App\Ai\Gpt\Providers\Yandex;

use Illuminate\Http\Client\Factory as HttpClient;
use Psr\Log\LoggerInterface;

readonly class Common
{
    private string $folderId;
    private string $apiKey;

    public function __construct(
        private HttpClient $client,
        private LoggerInterface $logger,
    ) {
        $this->folderId = config('services.yandex.folder_id');
        $this->apiKey = config('services.yandex.api_key');
    }

    protected function sendRequest(string $url, array $messages, float $temperature, int $maxTokens): ?array
    {
        try {
            $payload = [
                'modelUri' => "gpt://$this->folderId/yandexgpt/latest",
                'completionOptions' => [
                    'stream' => false,
                    'temperature' => $temperature,
                    'maxTokens' => (string) $maxTokens,
                ],
                'messages' => $messages,
            ];

            $response = $this->client
                ->timeout(60)
                ->withHeaders([
                    'Authorization' => "Api-Key $this->apiKey",
                    'x-folder-id' => $this->folderId,
                ])
                ->post($url, $payload);

            if (!$response->successful()) {
                $this->logger->error('YandexGPT request failed', [
                    'url' => $url,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            }

            return $response->json();

        } catch (\Exception $e) {
            $this->logger->error('Exception in YandexGPT request', [
                'url' => $url,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
