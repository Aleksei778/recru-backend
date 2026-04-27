<?php

declare(strict_types=1);

namespace App\Candidate\Services\Workplace;

use App\Candidate\Dto\Workplace\Create;
use App\Candidate\Models\WorkPlace;

final readonly class CrudService
{
    public function create(Create $dto): void
    {
        WorkPlace::create($dto->toArray());
    }
}
