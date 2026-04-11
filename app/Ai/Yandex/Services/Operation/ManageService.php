<?php

declare(strict_types=1);

namespace App\Ai\Yandex\Services\Operation;

use App\Ai\Yandex\Enum\OperationStatus;
use App\Ai\Yandex\Models\Operation;
use App\Ai\Yandex\Dto\Operation\{Create, Update};
use App\Ai\Yandex\Exceptions\OperationNotFoundException;
use Illuminate\Support\Facades\DB;
use Psr\Log\LoggerInterface;
use App\Ai\Yandex\Repositories\OperationRepository;
use Illuminate\Http\Client\Factory as HttpClient;

final readonly class ManageService
{
    private const OPERATIONS_URL = 'https://operation.api.cloud.yandex.net/operations/';

    public function __construct(
        private LoggerInterface $logger,
        private HttpClient $client,
        private OperationRepository $operationRepository,
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

    public function updateWithLock(Update $update): void
    {
        DB::transaction(function () use ($update)  {
            $operation = $this->operationRepository->find($update->id);

            if (!$operation) {
                throw new OperationNotFoundException(
                    message: 'Operation: CrudService: updateWithLock: Operation not found'
                );
            }

            $operation->lockForUpdate();
            $operation->update($update->toArray());
        });
    }

    public function create(Create $create): Operation
    {
        $operation = new Operation($create->toArray());

        $operation->save();

        return $operation;
    }

    public function updateStatus(int $id, OperationStatus $status): void
    {
        $this->updateWithLock(
            new Update(
                id: $id,
                status: $status,
            )
        );
    }
}
