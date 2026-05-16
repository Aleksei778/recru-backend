<?php

declare(strict_types=1);

namespace App\User\Http\Controllers\Profile;

use App\Common\Http\Controllers\Controller as BaseController;
use App\User\Http\Requests\Profile\UpdateUserRequest;
use App\User\Services\CrudService;
use Illuminate\Http\JsonResponse;

final readonly class UserController extends BaseController
{
    public function edit(UpdateUserRequest $request, CrudService $crudService): JsonResponse
    {
        $validated = $request->validated();

        $crudService->updatePersonalData(
            user: $request->user(),
            email: $validated['email'],
            name: $validated['name'],
            locale: $validated['locale'],
        );

        return response()->json([
            'message' => 'Your password has been updated.',
        ]);
    }
}
