<?php

declare(strict_types=1);

namespace App\Skill\Repositories;

use App\Skill\Models\Skill;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

final readonly class Repository
{
    public function find(int $id): ?Skill
    {
        return Skill::find($id);
    }

    public function findWithQueryAndLimit(string $q, int $limit): Collection
    {
        return Skill::with('category')
            ->when($q, function ($query) use ($q) {
                $query->where('name', 'ilike', "%$q%");
            })
            ->limit($limit)
            ->get();
    }
}
