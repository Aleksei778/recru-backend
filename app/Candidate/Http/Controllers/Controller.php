<?php

declare(strict_types=1);

namespace App\Candidate\Http\Controllers;

use App\Base\Http\Controllers\Controller as BaseController;
use App\Candidate\Dto\CreateDto;
use App\Candidate\Dto\UpdateDto;
use App\Candidate\Http\Requests\StoreRequest as StoreCandidateRequest;
use App\Candidate\Http\Requests\UpdateRequest as UpdateCandidateRequest;
use App\Candidate\Http\Resources\Resource as CandidateResource;
use App\Candidate\Models\Candidate;
use App\Candidate\Services\CrudService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

final readonly class Controller extends BaseController
{
    public function __construct(
        private CrudService $manageService,
    ) {
    }

    public function index(): AnonymousResourceCollection
    {
        $candidates = Candidate::with(['tenant', 'addedBy', 'interviews', 'skills'])
            ->latest()
            ->paginate(15);

        return CandidateResource::collection($candidates);
    }

    public function store(StoreCandidateRequest $request): CandidateResource
    {
        $dto = CreateDto::fromArray([
            ...$request->validated(),
            'added_by_id' => Auth::id(),
        ]);

        $candidate = $this->manageService->create($dto);

        return CandidateResource::make(
            $candidate->load(['tenant', 'addedBy', 'interviews'])
        );
    }

    public function show(Candidate $candidate): CandidateResource
    {
        return CandidateResource::make(
            $candidate->load(['tenant', 'addedBy', 'interviews', 'workPlaces', 'socials', 'skills'])
        );
    }

    public function update(UpdateCandidateRequest $request, Candidate $candidate): CandidateResource
    {
        $dto = UpdateDto::fromArray($request->validated());

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
