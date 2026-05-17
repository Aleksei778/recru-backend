<?php

declare(strict_types=1);

namespace App\User\Http\Controllers\Auth;

use App\Common\Http\Controllers\Controller;
use App\User\Http\Requests\Auth\LoginRequest;
use App\User\Repositories\Repository as UserRepository;
use App\User\Services\TokenService as UserTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

final readonly class LoginController extends Controller
{
    public function __construct(
        private UserRepository $userRepository,
        private UserTokenService $userTokenService,
    ) {
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = $this->userRepository->findByEmail($request->email);

        if (!$user || !Hash::check($request->password, $user->password)) {
            return new JsonResponse([
                'message' => 'Invalid credentials',
                'error' => [
                    'email' => ['The provided credentials are incorrect.'],
                ],
            ], 401);
        }

        $user->load('tenant');
        $this->userTokenService->dropAll($user);

        $token = $this->userTokenService->generate($user);

        return new JsonResponse([
            'user' => $user,
            'tenant' => $user->tenant,
            'token' => $token,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->userTokenService->dropCurrent($request->user());

        return new JsonResponse([
            'message' => 'Successfully logged out.',
        ]);
    }

    public function getMe(Request $request): JsonResponse
    {
        $user = request()->user();

        return new JsonResponse([
            'user' => $user,
            'tenant' => $user->tenant,
        ]);
    }
}
