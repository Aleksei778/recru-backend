<?php

declare(strict_types=1);

namespace App\Ai\Operation\Contracts;

use App\Ai\Operation\Models\Operation;

interface AsyncInterface
{
    public function getInfoAboutProviderOperation(Operation $operation): ?array;
    public function isDone(array $operationInfo): bool;
    public function hasError(array $operationInfo): bool;
}
