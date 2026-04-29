<?php

declare(strict_types=1);

namespace App\Candidate\Services\Social;

use App\Candidate\Dto\Social\Create;
use App\Candidate\Models\Candidate;

final readonly class SyncService
{
    public function __construct(
        private CrudService $crudService,
    ) {
    }

    public function syncSocials(Candidate $candidate, array $socials): void
    {
        foreach ($socials as $socialData) {
            $socialData['candidate_id'] = $candidate->id;
            $dto = Create::fromArray($socialData);

            $this->crudService->create($dto);
        }
    }
}
