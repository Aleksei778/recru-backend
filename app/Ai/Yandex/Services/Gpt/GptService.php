<?php

declare(strict_types=1);

namespace App\Ai\Yandex\Services\Gpt;

use App\Ai\Yandex\Dto\Gpt\Message;
use App\Ai\Yandex\Services\IamTokenService;
use Illuminate\Http\Client\Factory as HttpClient;
use Psr\Log\LoggerInterface;

final readonly class GptService
{
    private const COMPLETION_URL = 'https://llm.api.cloud.yandex.net/foundationModels/v1/completion';
    private string $folderId;

    public function __construct(
        private HttpClient $client,
        private IamTokenService $iamTokenService,
        private LoggerInterface $logger,
    ) {
        $this->folderId = config('services.yandex.folder_id');
    }

    /**
     * @param Message[] $messages
     * @param float $temperature
     * @param int $maxTokens
     * @return string|null
     */
    public function completion(array $messages, float $temperature = 0.3, int $maxTokens = 2000): ?string
    {
        try {
            $iamToken = $this->iamTokenService->getIamToken();

            $payload = [
                'modelUri' => "gpt://{$this->folderId}/yandexgpt/latest",
                'completionOptions' => [
                    'stream' => false,
                    'temperature' => $temperature,
                    'maxTokens' => (string) $maxTokens,
                ],
                'messages' => array_map(fn(Message $m) => $m->toArray(), $messages),
            ];

            $response = $this->client
                ->timeout(60)
                ->withHeaders([
                    'Authorization' => "Bearer $iamToken",
                    'x-folder-id' => $this->folderId,
                ])
                ->post(self::COMPLETION_URL, $payload);

            if (!$response->successful()) {
                $this->logger->error('YandexGPT completion failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            }

            $data = $response->json();
            
            return $data['result']['alternatives'][0]['message']['text'] ?? null;
        } catch (\Exception $e) {
            $this->logger->error('Exception in YandexGPT completion', [
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
