<?php

declare(strict_types=1);

namespace App\User\Http\Controllers\Auth;

use App\Base\Http\Controllers\Controller;
use App\Common\Http\Requests\Auth\LoginRequest;
use App\User\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

final class LoginController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return new JsonResponse([
                'message' => 'Invalid credentials',
                'error' => [
                    'email' => ['The provided credentials are incorrect.'],
                ],
            ], 401);
        }

        $user->load('tenant');
        $user->tokens()->delete();

        $token = $user->createToken('recru-hr-token')->plainTextToken;

        return new JsonResponse([
            'user' => $user,
            'tenant' => $user->tenant,
            'token' => $token,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return new JsonResponse([
            'message' => 'Successfully logged out.',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return new JsonResponse([
            'user' => $request->user(),
        ]);
    }
}
