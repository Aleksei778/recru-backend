<?php

declare(strict_types=1);

namespace App\Vacancy\Http\Controllers;

use App\Common\Http\Controllers\Controller as BaseController;
use App\Vacancy\Http\Requests\StoreRequest;
use App\Vacancy\Http\Requests\UpdateRequest;
use App\Vacancy\Http\Resources\Resource;
use App\Vacancy\Models\Vacancy;
use App\Vacancy\Services\CrudService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

final readonly class Controller extends BaseController
{
    public function __construct(
        private CrudService $manageService,
    ) {
    }

    public function index(): AnonymousResourceCollection
    {
        $vacancies = Vacancy::query()
            ->with(['createdBy', 'skills'])
            ->latest()
            ->paginate();

        return Resource::collection($vacancies);
    }

    public function store(StoreRequest $request): Resource
    {
        $vacancy = $this->manageService->create($request->validated());

        return Resource::make($vacancy);
    }

    public function show(Vacancy $vacancy): Resource
    {
        $vacancy->load(['createdBy', 'skills', 'tenant']);

        return Resource::make($vacancy);
    }

    public function update(UpdateRequest $request, Vacancy $vacancy): Resource
    {
        $vacancy = $this->manageService->update($vacancy, $request->validated());

        return Resource::make($vacancy);
    }

    public function destroy(Vacancy $vacancy): Response
    {
        $this->manageService->delete($vacancy);

        return response()->noContent();
    }
}
