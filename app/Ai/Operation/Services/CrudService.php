<?php

declare(strict_types=1);

namespace App\Ai\Operation\Services;

use App\Ai\Operation\Dto\{Create, Update};
use App\Ai\Operation\Models\Operation;

final readonly class CrudService
{
    public function create(Create $dto): Operation
    {
        $operation = new Operation($dto->toArray());

        $operation->save();

        return $operation;
    }

    public function update(Update $dto, Operation $operation): Operation
    {
        $operation->fill($dto->toArray());

        $operation->save();

        return $operation;
    }
}
