<?php

declare(strict_types=1);

namespace App\User\Http\Controllers\Auth;

use App\Common\Http\Controllers\Controller;
use App\User\Http\Requests\Auth\LoginRequest;
use App\User\Repositories\Repository as UserRepository;
use App\Tenant\Services\{CrudService as TenantCrudService};
use App\User\Services\{
    CrudService as UserCrudService,
    TokenService as UserTokenService,
};
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

final readonly class LoginController extends Controller
{
    public function __construct(
        private UserRepository $userRepository,
        private UserCrudService $userCrudService,
        private TenantCrudService $tenantCrudService,
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
        return new JsonResponse([
            'user' => $request->user(),
        ]);
    }

//    public function updateMe(): JsonResponse
//    {
//
//    }
}
