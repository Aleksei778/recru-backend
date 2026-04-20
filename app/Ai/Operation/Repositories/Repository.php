<?php

declare(strict_types=1);

namespace App\Ai\Operation\Repositories;

use App\Ai\Operation\Models\Operation;
use App\Ai\Yandex\Enum\OperationStatus;

final readonly class Repository
{
    public function find(int $id): ?Operation
    {
        return Operation::find($id);
    }

    public function findByProviderId(string $providerId): ?Operation
    {
        return Operation::where('provider_id', $providerId)->first();
    }

    /**
     * @param OperationStatus[] $statuses
     *
     * @return Operation[]
     */
    public function findByStatuses(array $statuses): array
    {
        return Operation::whereIn('status', $statuses)
            ->get()
            ->toArray();
    }
}
