<?php

declare(strict_types=1);

namespace App\Ai\Operation\Repositories;

use App\Ai\Operation\Models\Operation;

final readonly class Repository
{
    public function find(int $id): ?Operation
    {
        return Operation::find($id);
    }
}
