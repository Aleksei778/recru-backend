<?php

declare(strict_types=1);

namespace App\Interview\Repositories;

use App\Interview\Models\Interview;

final readonly class InterviewRepository
{
    public function find(int $id): ?Interview
    {
        return Interview::find($id);
    }
}
