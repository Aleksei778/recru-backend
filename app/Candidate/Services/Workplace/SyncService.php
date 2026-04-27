<?php

declare(strict_types=1);

namespace App\Candidate\Services\Workplace;

use App\Candidate\Dto\Workplace\Create;
use App\Candidate\Models\Candidate;

final readonly class SyncService
{
    public function __construct(
        private CrudService $crudService,
    ) {
    }

    public function syncWorkPlaces(Candidate $candidate, array $workPlaces): void
    {
        foreach ($workPlaces as $wpData) {
            $wpData['candidate_id'] = $candidate->id;
            $dto = Create::fromArray($wpData);

            $this->crudService->create($dto);
        }
    }
}
