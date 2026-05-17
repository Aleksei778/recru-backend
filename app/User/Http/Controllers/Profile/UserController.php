<?php

declare(strict_types=1);

namespace App\User\Http\Controllers\Profile;

use App\Common\Enum\Locale;
use App\Common\Http\Controllers\Controller as BaseController;
use App\User\Http\Requests\Profile\UpdateUserRequest;
use App\User\Http\Resources\Resource;
use App\User\Services\CrudService;
use Illuminate\Http\JsonResponse;

final readonly class UserController extends BaseController
{
    public function edit(UpdateUserRequest $request, CrudService $crudService): Resource|JsonResponse
    {
        $validated = $request->validated();

        $crudService->updatePersonalData(
            user: $request->user(),
            email: $validated['email'],
            name: $validated['name'],
            locale: Locale::tryFrom($validated['locale']),
        );

        $user = $request->user()->refresh();

        return Resource::make($user->load('tenant'));
    }
}
