<?php

declare(strict_types=1);

namespace App\Candidate\Http\Controllers;

use App\Base\Http\Controllers\Controller as BaseController;
use App\Candidate\Http\Requests\StoreRequest as StoreCandidateRequest;
use App\Candidate\Http\Resources\Resource as CandidateResource;
use App\Candidate\Models\Candidate as CandidateModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

final readonly class Controller extends BaseController
{
    public function index(): ResourceCollection
    {
        return CandidateResource::collection(
            CandidateModel::with(['tenant', 'addedBy', 'interview'])
                ->paginate(15)
        );
    }

    public function store(StoreCandidateRequest $request): CandidateResource
    {
        $candidate = new CandidateModel($request->validated());

        return new CandidateResource(
            $candidate->load(['tenant', 'addedBy', 'interview'])
        );
    }

    public function show(CandidateModel $candidate): CandidateResource
    {
        return new CandidateResource(
            $candidate->load(['tenant', 'addedBy', 'interview'])
        );
    }

    public function destroy(CandidateModel $candidate): JsonResponse
    {
        $candidate->delete();

        return response()
            ->json(null, 204);
    }
}
