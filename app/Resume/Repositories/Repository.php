<?php

declare(strict_types=1);

namespace App\Resume\Repositories;

use App\Resume\Models\Resume;

final readonly class Repository
{
    public function find(int $id): ?Resume
    {
        return Resume::find($id);
    }
}
