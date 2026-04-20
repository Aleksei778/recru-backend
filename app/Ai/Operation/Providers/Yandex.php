<?php

declare(strict_types=1);

namespace App\Ai\Operation\Providers;

use App\Ai\Operation\Contracts\AsyncInterface;
use Psr\Log\LoggerInterface;
use Illuminate\Http\Client\Factory as HttpClient;
use App\Ai\Operation\Models\Operation;

final readonly class Yandex implements AsyncInterface
{
    private const string OPERATIONS_URL = 'https://operation.api.cloud.yandex.net/operations/';

    private string $folderId;
    private string $apiKey;

    public function __construct(
        private LoggerInterface $logger,
        private HttpClient $client,
    ) {
        $this->folderId = config('services.yandex.folder_id');
        $this->apiKey = config('services.yandex.api_key');
    }

    public function getInfoAboutProviderOperation(Operation $operation): ?array
    {
        try {
            $response = $this->client
                ->timeout(30)
                ->withHeaders([
                    'Authorization' => "Api-Key $this->apiKey",
                    'x-folder-id' => $this->folderId,
                ])
                ->get(self::OPERATIONS_URL . $operation->provider_id);

            if (!$response->successful()) {
                $this->logger->error('Yandex Operation fetch failed.', [
                    'id' => $operation->id,
                    'yandex_id' => $operation->provider_id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            }

            return $response->json();

        } catch (\Exception $e) {
            $this->logger->error('Exception while fetching Yandex Operation', [
                'id' => $operation->id,
                'yandex_id' => $operation->provider_id,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public function isDone(array $operationInfo): bool
    {
        return isset($operationInfo['done']) && $operationInfo['done'];
    }

    public function hasError(array $operationInfo): bool
    {
        return array_key_exists('error', $operationInfo);
    }
}
