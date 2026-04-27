<?php

declare(strict_types=1);

namespace App\Candidate\Services\Workplace;

use App\Candidate\Dto\Workplace\Create;
use App\Candidate\Models\{Candidate, WorkPlace};

final readonly class CrudService
{
    public function create(Create $dto): void
    {
        WorkPlace::create($dto->toArray());
    }

    public function syncWorkPlaces(Candidate $candidate, array $workPlaces): void
    {
        foreach ($workPlaces as $wpData) {
            $wpData['candidate_id'] = $candidate->id;
            $dto = Create::fromArray($wpData);

            $this->create($dto);
        }
    }
}
