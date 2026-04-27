<?php

declare(strict_types=1);

namespace App\Ai\Operation\Providers;

use App\Ai\Operation\Models\Operation;

interface OperationInterface
{
    public function getInfoAboutProviderOperation(Operation $operation): ?array;
    public function isDone(array $operationInfo): bool;
    public function hasError(array $operationInfo): bool;
}
