<?php

declare(strict_types=1);

namespace App\User\Http\Controllers\Auth;

use App\Common\Http\Controllers\Controller;
use App\User\Http\Requests\Auth\RegisterRequest;
use App\Tenant\Services\{CrudService as TenantCrudService};
use App\User\Enum\UserRole;
use App\User\Services\{
    CrudService as UserCrudService,
    TokenService as UserTokenService,
};
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\{DB, Hash};
use Psr\Log\LoggerInterface;

final readonly class RegisterController extends Controller
{
    public function __construct(
        private LoggerInterface $logger,
        private UserCrudService $userCrudService,
        private TenantCrudService $tenantCrudService,
        private UserTokenService $userTokenService,
    ) {
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            $email = $validated['email'];
            $password = $validated['password'];

            $tenant = $this->tenantCrudService->create(
                name: $validated['company'],
                subdomain: $validated['subdomain']
            );

            $user = $this->userCrudService->create(
                tenant: $tenant,
                email: $email,
                password: Hash::make($password),
                role: UserRole::ADMIN,
            );

            $token = $this->userTokenService->generate($user);

            DB::commit();

            $this->logger->info('Successfully registered tenant with user', [
                'tenant' => $tenant,
                'user' => $user,
            ]);

            return new JsonResponse([
                'user' => $user,
                'tenant' => $tenant,
                'token' => $token,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            $this->logger->error('Error registering tenant with user', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return new JsonResponse([
                'message' => 'Registration failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
