<?php

declare(strict_types=1);

namespace App\Resume\Services;

use App\Resume\Models\Resume;
use App\Candidate\Dto\Candidate\{Update};
use App\Candidate\Enum\EducationLevel;
use App\Candidate\Repositories\{Repository as CandidateRepository};
use App\Candidate\Services\{CrudService as CandidateCrudService};
use App\Candidate\Services\Social\{SyncService as SocialSyncService};
use App\Candidate\Services\Workplace\{SyncService as WorkplaceSyncService};
use Illuminate\Support\Facades\DB;

final readonly class AttachToCandidateService
{
    public function __construct(
        private CandidateRepository $candidateRepository,
        private CandidateCrudService $candidateService,
        private SocialSyncService $socialService,
        private WorkplaceSyncService $workplaceService,
    ) {
    }

    public function attachToCandidate(Resume $resume, int $candidateId): bool
    {
        $candidate = $this->candidateRepository->find($candidateId);
        $data = $resume->parsed_data;

        if (!$data) {
            return false;
        }

        return DB::transaction(function () use ($resume, $candidate, $data) {
            $resume->setCandidateId($candidate->id);

            $updateCandidateDto = new Update(
                firstName: (string) ($data['first_name'] ?? $candidate->first_name),
                lastName: (string) ($data['last_name'] ?? $candidate->last_name),
                middleName: (string) ($data['middle_name'] ?? $candidate->middle_name),
                email: (string) ($data['email'] ?? $candidate->email),
                phone: (string) ($data['phone'] ?? $candidate->phone),
                experienceYears: isset($data['experience_years'])
                    ? (int) $data['experience_years']
                    : $candidate->experience_years,
                educationLevel: isset($data['education_level'])
                    ? EducationLevel::tryFrom($data['education_level']) ?? $candidate->education_level
                    : $candidate->education_level,
            );

            $this->candidateService->update($candidate, $updateCandidateDto);

            $this->socialService->syncSocials($candidate, $data);
            $this->workplaceService->syncWorkplaces($candidate, $data);

            return true;
        });
    }
}
