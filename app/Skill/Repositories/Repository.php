<?php

declare(strict_types=1);

namespace App\Skill\Repositories;

use App\Skill\Models\Skill;
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

    /**
     * @param  int[]  $ids
     * @return int[]
     */
    public function findIdsByIds(array $ids): array
    {
        return Skill::whereIn('id', $ids)->pluck('id')->all();
    }

    /**
     * @param  string[]  $names
     * @return int[]
     */
    public function findIdsByNames(array $names): array
    {
        $normalized = array_map('mb_strtolower', $names);

        return Skill::get(['id', 'name', 'aliases'])
            ->filter(function (Skill $skill) use ($normalized) {
                if (in_array(mb_strtolower($skill->name), $normalized, true)) {
                    return true;
                }

                return array_any((array)$skill->aliases, fn($alias) => in_array(mb_strtolower($alias), $normalized, true));

            })
            ->pluck('id')
            ->all();
    }
}
