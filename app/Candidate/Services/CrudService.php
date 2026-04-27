<?php

declare(strict_types=1);

namespace App\Candidate\Services;

use App\Candidate\Dto\Candidate\{Create, Update};
use App\Candidate\Enum\{EducationLevel, Source, Status};
use App\Candidate\Models\Candidate;
use App\Candidate\Repositories\Repository;
use App\Candidate\Services\Workplace\CrudService;
use App\Resume\Dto\FinalResult;

final readonly class CrudService
{
    public function __construct(
        private Social\CrudService $socialService,
        private CrudService        $workplaceService,
        private Repository         $candidateRepository,
    ) {
    }

    public function create(Create $dto): Candidate
    {
        $data = $dto->toArray();
        if (!isset($data['status'])) {
            $data['status'] = Status::NEW;
        }

        return Candidate::create($data);
    }

    public function update(Candidate $candidate, Update $dto): Candidate
    {
        $candidate->update($dto->toArray());

        return $candidate;
    }

    public function delete(Candidate $candidate): bool
    {
        return (bool) $candidate->delete();
    }

    private function findOrCreateCandidateFromParsedResume(
        FinalResult $dto,
        ?int        $tenantId,
        ?int        $userId
    ): Candidate {
        $candidate = null;

        if ($dto->email) {
            $candidate = $this->candidateRepository->findByEmail($dto->email);
        }

        if (!$candidate && $dto->phone) {
            $candidate = $this->candidateRepository->findByPhone($dto->phone);
        }

        $data = [
            'first_name' => $dto->first_name,
            'last_name' => $dto->last_name,
            'middle_name' => $dto->middle_name,
            'email' => $dto->email,
            'phone' => $dto->phone,
            'experience_years' => $dto->experience_years,
            'education_level' => EducationLevel::from($dto->education_level),
        ];

        if ($candidate) {
            $candidate->update($data);
        } else {
            $data['tenant_id'] = $tenantId;
            $data['added_by_id'] = $userId;
            $data['source'] = Source::BULK_IMPORT;
            $data['status'] = Status::NEW;

            $candidate = Candidate::create($data);
        }

        $this->socialService->syncSocials($candidate, $dto->socials);
        $this->workplaceService->syncWorkPlaces($candidate, $dto->work_places);

        return $candidate;
    }
}
