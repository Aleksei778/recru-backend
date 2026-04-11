<?php

declare(strict_types=1);

namespace App\Interview\Services;

use App\Interview\Dto\Create;
use App\Interview\Models\Interview;

final readonly class CreateService
{
    public function create(Create $dto): Interview
    {
        $interview = new Interview($dto->toArray());

        $interview->save();

        return $interview;
    }
}
