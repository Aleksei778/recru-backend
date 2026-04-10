<?php

declare(strict_types=1);

namespace App\Ai\Yandex\Repositories;

use App\Ai\Yandex\Enum\OperationStatus;
use App\Ai\Yandex\Models\Operation;

final readonly class OperationRepository
{
    public function find(int $id): ?Operation
    {
        return Operation::find($id);
    }

    /**
     * Find operations by their statuses.
     *
     * @param OperationStatus[] $statuses Array of operation statuses to filter by.
     * @return \Illuminate\Database\Eloquent\Collection<int, Operation> Collection of operation models matching the given statuses.
     */
    public function findByStatuses(array $statuses): \Illuminate\Database\Eloquent\Collection
    {
        return Operation::whereIn('status', $statuses)->get();
    }
}
