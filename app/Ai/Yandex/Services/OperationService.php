<?php

declare(strict_types=1);

namespace App\Ai\Yandex\Services;

use Psr\Log\LoggerInterface;
use Illuminate\Http\Client\Factory as HttpClient;

final readonly class OperationService
{
    private const OPERATIONS_URL = 'https://operation.api.cloud.yandex.net/operations/';

    public function __construct(
       private LoggerInterface $logger,
       private HttpClient $client,
       private string $apiKey,
    ) {
    }

    public function get(string $operationId): ?array
    {
        try {
            $response = $this->client
                ->timeout(30)
                ->withHeaders([
                    'Authorization' => "Api-Key $this->apiKey"
                ])
                ->get(self::OPERATIONS_URL . $operationId);

            if (!$response->successful()) {
                $this->logger->error('Yandex Operation fetch failed.', [
                    'operation_id' => $operationId,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            }

            return $response->json();
        } catch (\Exception $e) {
            $this->logger->error('Exception while fetching Yandex Operation', [
                'operation_id' => $operationId,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
