<?php

declare(strict_types=1);

namespace App\Vacancy\Repositories;

use App\Vacancy\Models\Vacancy;

final readonly class VacancyRepository
{
    public function find(int $id): ?Vacancy
    {
        return Vacancy::find($id);
    }
}
