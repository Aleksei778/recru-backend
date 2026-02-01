<?php

declare(strict_types=1);

namespace App\Yandex\Services;

use Illuminate\Support\Facades\Http;
use Psr\Log\LoggerInterface;

final readonly class OperationInfoService
{
    public function __construct(
        private IamTokenService $iamTokenService,
        private LoggerInterface $logger,
    ) {
    }

    public function getInfoAboutOperation(string $yandexId): ?array
    {
        try {
            $iamToken = $this->iamTokenService->getIamToken();

            $operationUrl = config('services.yandex.operation_url');

            $response = Http::timeout(60)
                ->withHeaders([
                    'Authorization' => "Bearer $iamToken",
                    'Content-Type' => 'application/json',
                ])
                ->get($operationUrl . urlencode($yandexId));

            $data = $response->json();

            if ($response->successful()) {
                $this->logger->info('Operation status was successfully parsed', [
                    'data' => $data,
                ]);

                return $data;
            }

            $this->logger->error('Yandex API returned error when fetching operation status.', [
                'data' => $data,
                'status' => $response->status(),
            ]);

            return null;
        } catch (\Exception $e) {
            $this->logger->error('Exception thrown while fetching operation info.', [
                'yandex_id' => $yandexId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }
}
