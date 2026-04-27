<?php

declare(strict_types=1);

namespace App\Candidate\Services\Social;

use App\Candidate\Models\Social;
use App\Candidate\Dto\Social\Create;

final readonly class CrudService
{
    public function create(Create $dto): void
    {
        Social::create($dto->toArray());
    }
}
