<?php

declare(strict_types=1);

namespace App\User\Http\Controllers\Team;

use App\Common\Enum\Locale;
use App\Common\Http\Controllers\Controller as BaseController;
use App\User\Enum\UserRole;
use App\User\Repositories\Repository;
use App\User\Http\Requests\Team\{UpdateRequest, StoreRequest};
use App\User\Http\Resources\{Collection, Resource};
use App\User\Models\User;
use App\User\Services\CrudService;
use Illuminate\Http\JsonResponse;

final readonly class Controller extends BaseController
{
    public function index(Repository $repository): Collection
    {
        return Collection::make(
            $repository->findHr()
        );
    }

    public function show(User $team): Resource
    {
        abort_if($team->role !== UserRole::HR, 404);

        return new Resource($team);
    }

    public function store(StoreRequest $request, CrudService $crudService): JsonResponse
    {
        $validated = $request->validated();

        $member = $crudService->createHr(
            tenant: $request->user()->tenant,
            email: $validated['email'],
            password: $validated['password'],
            name: $validated['name'] ?? null,
            locale: Locale::from($validated['locale']),
        );

        return response()->json(new Resource($member), 201);
    }

    public function update(UpdateRequest $request, User $team, CrudService $crudService): JsonResponse
    {
        abort_if($team->role !== UserRole::HR, 404);

        $validated = $request->validated();

        $crudService->updatePersonalData(
            user: $team,
            email: $validated['email'],
            name: $validated['name'] ?? null,
            locale: Locale::from($validated['locale']),
        );

        if (!empty($validated['password'])) {
            $crudService->updatePassword($team, $validated['password']);
        }

        return response()->json(new Resource($team->fresh()));
    }

    public function destroy(User $team, CrudService $crudService): JsonResponse
    {
        abort_if($team->role !== UserRole::HR, 404);

        $crudService->delete($team);

        return response()->json(['message' => 'Member has been removed.']);
    }
}
