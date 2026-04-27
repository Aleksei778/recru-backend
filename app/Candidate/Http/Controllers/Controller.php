<?php

declare(strict_types=1);

namespace App\Candidate\Http\Controllers;

use App\Candidate\Dto\Candidate\{Create, Update};
use App\Candidate\Http\Requests\{StoreRequest, UpdateRequest};
use App\Candidate\Http\Resources\Collection;
use App\Candidate\Http\Resources\Resource;
use App\Candidate\Models\Candidate;
use App\Candidate\Services\{Social\CrudService, Social\CrudService, Workplace\CrudService,};
use App\Common\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

final readonly class Controller extends BaseController
{
    public function __construct(
        private CrudService $crudService,
        private CrudService $workplaceService,
        private CrudService $socialService,
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

        $candidate = $this->crudService->create($dto);

        $this->workplaceService->syncWorkPlaces($candidate, $validated['work_places'] ?? []);
        $this->socialService->syncSocials($candidate, $validated['socials'] ?? []);

        return Resource::make(
            $candidate->load(['tenant', 'addedBy', 'interviews'])
        );
    }

    public function show(Candidate $candidate): CandidateResource
    {
        return CandidateResource::make(
            $candidate->load(['tenant', 'addedBy', 'interviews', 'workPlaces', 'socials', 'skills'])
        );
    }

    public function update(UpdateRequest $request, Candidate $candidate): Resource
    {
        $dto = Update::fromArray($request->validated());

        $candidate = $this->manageService->update($candidate, $dto);

        return CandidateResource::make(
            $candidate->load(['tenant', 'addedBy', 'interviews'])
        );
    }

    public function destroy(Candidate $candidate): Response
    {
        $this->manageService->delete($candidate);

        return response()->noContent();
    }
}
