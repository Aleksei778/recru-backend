<?php

declare(strict_types=1);

namespace App\Skill\Services;

use App\Candidate\Models\Candidate;
use App\Skill\Repositories\Repository;
use App\Vacancy\Models\Vacancy;
use Illuminate\Database\Eloquent\Model;

final readonly class SyncService
{
    public function __construct(
        private Repository $repository,
    ) {
    }

    public function syncSkillsByNames(Vacancy|Candidate $model, array $names): void
    {
        $model->skills()->sync(
            ids: $this->repository->findIdsByNames($names)
        );
    }
}
