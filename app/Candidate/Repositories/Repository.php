<?php

declare(strict_types=1);

namespace App\Candidate\Repositories;

use App\Candidate\Models\Candidate;
use Illuminate\Database\Eloquent\Collection;

final readonly class Repository
{
    public function find(int $id): ?Candidate
    {
        return Candidate::find($id);
    }

    public function findWithQueryAndLimit(string $q, int $limit): Collection
    {
        return Candidate::when($q, function ($query) use ($q) {
                $query->where('first_name', 'ilike', "%$q%")
                    ->orWhere('last_name', 'ilike', "%$q%")
                    ->orWhere('email', 'ilike', "%$q%")
                    ->orWhere('middle_name', 'ilike', "%$q%");
            })
            ->limit($limit)
            ->get();
    }
}
