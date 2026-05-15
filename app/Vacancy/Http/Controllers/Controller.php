<?php

declare(strict_types=1);

namespace App\Vacancy\Http\Controllers;

use App\Common\Http\Controllers\Controller as BaseController;
use App\Skill\Services\SyncService as SkillService;
use App\Vacancy\Dto\Create;
use App\Vacancy\Dto\Update;
use Illuminate\Http\JsonResponse;
use App\Vacancy\Http\Requests\{StoreRequest, UpdateRequest};
use App\Vacancy\Http\Resources\{Resource, Collection};
use App\Vacancy\Models\Vacancy;
use App\Vacancy\Services\CrudService;
use Illuminate\Http\Response;

final readonly class Controller extends BaseController
{
    public function __construct(
        private CrudService $manageService,
        private SkillService $skillService,
    ) {
    }

    public function index(): Collection
    {
        $vacancies = Vacancy::query()
            ->with(['createdBy', 'skills'])
            ->latest()
            ->paginate();

        return Collection::make($vacancies);
    }

    public function store(StoreRequest $request): Resource
    {
        $validated = $request->validated();
        $vacancy = $this->manageService->create(Create::fromArray($validated));
        $this->skillService->syncSkillsByIds($vacancy, $validated['skill_ids'] ?? []);

        return Resource::make($vacancy->load('skills'));
    }

    public function show(string $subdomain, Vacancy $vacancy): Resource
    {
        $vacancy->load(['createdBy', 'skills', 'tenant']);

        return Resource::make($vacancy);
    }

    public function update(UpdateRequest $request, string $subdomain, Vacancy $vacancy): Resource
    {
        $validated = $request->validated();
        $vacancy = $this->manageService->update($vacancy, Update::fromArray($validated));

        if (array_key_exists('skill_ids', $validated)) {
            $this->skillService->syncSkillsByIds($vacancy, $validated['skill_ids'] ?? []);
        }

        return Resource::make($vacancy->load('skills'));
    }

    public function destroy(string $subdomain, Vacancy $vacancy): JsonResponse
    {
        $this->manageService->delete($vacancy);

        return response()->json([
            'message' => 'Successfully deleted vacancy.'
        ]);
    }
}
