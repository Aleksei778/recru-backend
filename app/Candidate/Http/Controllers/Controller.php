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
    Http\Response,
    Http\Request,
    Support\Collection as SupportCollection,
    Support\Facades\Auth
};

final readonly class Controller extends BaseController
{
    public function __construct(
        private CandidateService $candidateService,
        private WorkplaceService $workplaceService,
        private SocialService $socialService,
        private SkillService $skillService,
        private CandidateRepository $candidateRepository,
    ) {
    }

    public function index(): Collection
    {
        $candidates = Candidate::with(['tenant', 'addedBy', 'interviews', 'skills'])
            ->latest()
            ->paginate(15);

        return Collection::make($candidates);
    }

    public function store(StoreRequest $request): Resource
    {
        $validated = $request->validated();

        $dto = Create::fromArray([
            ...$request->validated(),
            'added_by_id' => Auth::id(),
        ]);

        $candidate = $this->candidateService->create($dto);

        $this->workplaceService->syncWorkPlaces($candidate, $validated['workplaces'] ?? []);
        $this->socialService->syncSocials($candidate, $validated['socials'] ?? []);
        $this->skillService->syncSkillsByNames($candidate, $validated['skills'] ?? []);

        return Resource::make(
            $candidate->load(['tenant', 'addedBy', 'interviews'])
        );
    }

    public function show(Candidate $candidate): Resource
    {
        return Resource::make(
            $candidate->load(['tenant', 'addedBy', 'interviews', 'workPlaces', 'socials', 'skills'])
        );
    }

    public function update(UpdateRequest $request, Candidate $candidate): Resource
    {
        $dto = Update::fromArray($request->validated());

        $candidate = $this->candidateService->update($candidate, $dto);

        return Resource::make(
            $candidate->load(['tenant', 'addedBy', 'interviews'])
        );
    }

    public function destroy(Candidate $candidate): Response
    {
        $this->candidateService->delete($candidate);

        return response()->noContent();
    }

    public function search(Request $request): SupportCollection
    {
        $validated = $request->validate([
            'q' => 'required|string',
        ]);

        return $this->candidateRepository->findWithQueryAndLimit($validated['q'], 20);
    }
}
