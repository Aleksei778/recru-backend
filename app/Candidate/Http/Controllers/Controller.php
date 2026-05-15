<?php

declare(strict_types=1);

namespace App\Candidate\Http\Controllers;

use App\Candidate\{
    Http\Requests\StoreRequest,
    Http\Requests\UpdateRequest,
    Http\Resources\Collection,
    Http\Resources\Resource,
    Models\Candidate,
    Services\Social\SyncService as SocialService,
    Services\CrudService as CandidateService,
    Services\Workplace\SyncService as WorkplaceService,
    Dto\Candidate\Create,
    Dto\Candidate\Update,
    Repositories\Repository as CandidateRepository,
};
use App\Skill\Services\SyncService as SkillService;
use App\Common\Http\Controllers\Controller as BaseController;
use Illuminate\{
    Http\JsonResponse,
    Http\Request,
    Support\Collection as SupportCollection,
    Support\Facades\Auth
};
use Psr\Log\LoggerInterface;

final readonly class Controller extends BaseController
{
    public function __construct(
        private CandidateService $candidateService,
        private WorkplaceService $workplaceService,
        private SocialService $socialService,
        private SkillService $skillService,
        private CandidateRepository $candidateRepository,
        private LoggerInterface $logger
    ) {
    }

    public function index(): Collection
    {
        $candidates = Candidate::with([
            'tenant',
            'addedBy',
            'interviews',
            'skills',
            'workPlaces',
            'socials',
        ])
            ->latest()
            ->paginate(15);

        return Collection::make($candidates);
    }

    public function store(StoreRequest $request): Resource
    {
        $validated = $request->validated();

        $this->logger->info('Информация по новому кандидату', [
            'candidate' => $validated,
        ]);

        $dto = Create::fromArray([
            ...$request->validated(),
            'added_by_id' => Auth::id(),
        ]);

        $candidate = $this->candidateService->create($dto);

        $this->workplaceService->syncWorkPlaces($candidate, $validated['workplaces'] ?? []);
        $this->socialService->syncSocials($candidate, $validated['socials'] ?? []);
        $this->skillService->syncSkillsByIds($candidate, $validated['skill_ids'] ?? []);

        return Resource::make(
            $candidate->load([
                'tenant',
                'addedBy',
                'interviews',
                'skills',
                'workPlaces',
                'socials',
            ])
        );
    }

    public function show(string $subdomain, Candidate $candidate): Resource
    {
        return Resource::make(
            $candidate->load([
                'tenant',
                'addedBy',
                'interviews',
                'skills',
                'workPlaces',
                'socials',
            ])
        );
    }

    public function update(
        UpdateRequest $request,
        string $subdomain,
        Candidate $candidate
    ): Resource {
        $validated = $request->validated();
        $dto = Update::fromArray($validated);

        $candidate = $this->candidateService->update($candidate, $dto);

        if (array_key_exists('skill_ids', $validated)) {
            $this->skillService->syncSkillsByIds($candidate, $validated['skill_ids'] ?? []);
        }

        return Resource::make(
            $candidate->load([
                'tenant',
                'addedBy',
                'interviews',
                'skills',
                'workPlaces',
                'socials',
            ])
        );
    }

    public function destroy(string $subdomain, Candidate $candidate): JsonResponse
    {
        $this->candidateService->delete($candidate);

        return response()->json(['message' => 'Candidate successfully deleted.']);
    }

    public function search(Request $request): SupportCollection
    {
        $validated = $request->validate([
            'q' => 'required|string',
        ]);

        return $this->candidateRepository->findWithQueryAndLimit($validated['q'], 20);
    }
}
