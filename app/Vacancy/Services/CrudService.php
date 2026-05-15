<?php

declare(strict_types=1);

namespace App\Vacancy\Services;

use App\Vacancy\Dto\{Create, Update};
use App\Vacancy\Models\Vacancy;

final readonly class CrudService
{
    public function create(Create $dto): Vacancy
    {
        $vacancy = new Vacancy($dto->toArray());

        $vacancy->setCreatedByAttribute();

        if ($vacancy->isPublished()) {
            $vacancy->setPublishedAtAttribute();
        }

        $vacancy->save();

        return $vacancy;
    }

    public function update(Vacancy $vacancy, Update $dto): Vacancy
    {
        $data = $dto->toArray();

        if ($dto->isPublished() && $vacancy->isDraft()) {
            $vacancy->setPublishedAtAttribute();
        }

        if ($dto->isClosed() && $vacancy->isPublished()) {
            $vacancy->setClosedAtAttribute();
        }

        $vacancy->update($data);

        return $vacancy;
    }

    public function delete(Vacancy $vacancy): bool
    {
        return $vacancy->delete();
    }
}
