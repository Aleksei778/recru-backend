<?php

declare(strict_types=1);

namespace App\Ai\Gpt\Providers;

use App\Ai\Operation\Models\Operation;
use App\Ai\Gpt\Contracts\{AsyncInterface, SyncInterface};
use App\Ai\Gpt\Dto\Message;
use Illuminate\Http\Client\Factory as HttpClient;
use Psr\Log\LoggerInterface;
use App\Ai\Operation\Providers\Yandex as OperationProvider;

final readonly class Yandex implements SyncInterface, AsyncInterface
{
    private const string SYNC_URL = 'https://llm.api.cloud.yandex.net/foundationModels/v1/completion';
    private const string ASYNC_URL = 'https://llm.api.cloud.yandex.net/foundationModels/v1/completionAsync';

    private string $folderId;
    private string $apiKey;

    public function __construct(
        private HttpClient $client,
        private LoggerInterface $logger,
        private OperationProvider $operationProvider,
    ) {
        $this->folderId = config('services.yandex.folder_id');
        $this->apiKey = config('services.yandex.api_key');
    }

    /**
     * @param Message[] $messages
     * @param float $temperature
     * @param int $maxTokens
     *
     * @return string|null
     */
    public function completionSync(array $messages, float $temperature = 0.3, int $maxTokens = 2000): ?string
    {
        $data = $this->request(self::SYNC_URL, $messages, $temperature, $maxTokens);

        return $data['result']['alternatives'][0]['message']['text'] ?? null;
    }

    /**
     * @param Message[] $messages
     * @param float $temperature
     * @param int $maxTokens
     *
     * @return string|null
     */
    public function completionAsync(array $messages, float $temperature = 0.3, int $maxTokens = 2000): ?string
    {
        $data = $this->request(self::ASYNC_URL, $messages, $temperature, $maxTokens);

        return $data['id'] ?? null;
    }

    public function getCompletionResult(Operation $operation): ?string
    {
        $yandexOperation = $this->operationProvider->getInfoAboutProviderOperation($operation->provider_id);
        if (!($yandexOperation['done'] ?? false)) return null;

        return $yandexOperation['response']['alternatives'][0]['message']['text'] ?? null;
    }

    private function request(string $url, array $messages, float $temperature, int $maxTokens): ?array
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
