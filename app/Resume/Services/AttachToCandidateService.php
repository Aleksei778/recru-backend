<?php

declare(strict_types=1);

namespace App\Resume\Services;

use App\Candidate\Enum\Source;
use App\Candidate\Models\Candidate;
use App\Resume\Models\Resume;
use App\Candidate\Dto\Candidate\{Update, Create};
use App\Candidate\Enum\EducationLevel;
use App\Candidate\Repositories\{Repository as CandidateRepository};
use App\Candidate\Services\{
    CrudService as CandidateCrudService,
    Social\SyncService as SocialSyncService,
    Workplace\SyncService as WorkplaceSyncService,
};
use App\Skill\Services\{SyncService as SkillSyncService};
use Illuminate\Support\Facades\DB;

final readonly class AttachToCandidateService
{
    public function __construct(
        private CandidateRepository $candidateRepository,
        private CandidateCrudService $candidateService,
        private SocialSyncService $socialService,
        private WorkplaceSyncService $workplaceService,
        private SkillSyncService $skillsService,
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

            $this->socialService->syncSocials($candidate, $data['socials'] ?? []);
            $this->workplaceService->syncWorkplaces($candidate, $data['workplaces'] ?? []);
            $this->skillsService->syncSkillsByNames($candidate, $data['skills'] ?? []);

            return true;
        });
    }

    public function createCandidateFromResume(Resume $resume): Candidate
    {
        $data = $resume->parsed_data ?? [];

        return DB::transaction(function () use ($resume, $data) {
            $candidate = $this->candidateService->create(new Create(
                firstName: (string) $data['first_name'],
                lastName: (string) $data['last_name'],
                middleName: (string) $data['middle_name'],
                email: (string) $data['email'],
                phone: (string) $data['phone'],
                source: Source::tryFrom($data['source']),
                experienceYears: (float) $data['experience_years'],
                educationLevel: EducationLevel::tryFrom($data['education_level'])
            ));

            $resume->setCandidateId($candidate->id);

            $this->socialService->syncSocials($candidate, $data['socials'] ?? []);
            $this->workplaceService->syncWorkplaces($candidate, $data['workplaces'] ?? []);
            $this->skillsService->syncSkillsByNames($candidate, $data['skills'] ?? []);

            return true;
        });
    }
}
