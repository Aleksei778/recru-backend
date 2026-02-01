<?php

declare(strict_types=1);

namespace App\User\Http\Controllers\Auth;

use App\Common\Http\Controllers\Controller;
use App\User\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

final class LoginController extends Controller
{
    /**
     * Login and issue sanctum token.
     * @throws ValidationException
     */
    public function store(LoginRequest $request): JsonResponse
    {
        if (!Auth::guard('tenant-web')
            ->attempt($request->only('email', 'password'), $request->boolean('remember'))
        ) {
            throw ValidationException::withMessages([
                'loginFail' => 'Incorrect email or password.',
            ]);
        }

        $user = Auth::guard('tenant-web')->user();

        $user->tokens()->delete();

        $token = $user->createToken('recru-token')->plainTextToken;

        return new JsonResponse([
            'token' => $token,
            'user' => $user,
        ]);
    }

    /**
     * Logout and revoke token.
     */
    public function destroy(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return new JsonResponse([
            'message' => 'Successfully logged out.',
        ]);
    }
}
