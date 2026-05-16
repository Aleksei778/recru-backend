<?php

declare(strict_types=1);

namespace App\User\Http\Controllers\Profile;

use App\Common\Http\Controllers\Controller as BaseController;
use App\User\Http\Requests\Profile\UpdatePasswordRequest;
use App\User\Services\CrudService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

final readonly class PasswordController extends BaseController
{
    public function edit(UpdatePasswordRequest $request, CrudService $crudService): JsonResponse
    {
        $validated = $request->validated();
        $passwordIncorrect = !Hash::check($validated['current_password'], $request->user()->password);

        if ($passwordIncorrect) {
            return response()->json([
                'message' => 'Your current password is incorrect.',
            ], status: 422);
        }

        $crudService->updatePassword($request->user(), $validated['new_password']);

        return response()->json([
            'message' => 'Your password has been updated.',
        ]);
    }
}
