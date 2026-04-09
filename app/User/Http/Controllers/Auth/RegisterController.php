<?php

declare(strict_types=1);

namespace App\User\Http\Controllers\Auth;

use App\Base\Http\Controllers\Controller;
use App\User\Http\Requests\Auth\RegisterRequest;
use App\Tenant\Models\Tenant;
use App\User\Enum\UserRole;
use App\User\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Psr\Log\LoggerInterface;

final readonly class RegisterController extends Controller
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            $company = $validated['company'];
            $subdomain = $validated['subdomain'];
            $email = $validated['email'];
            $password = $validated['password'];

            $tenant = Tenant::create([
                'name' => $company,
            ]);

            $tenant->domains()->create([
                'domain' => $subdomain,
            ]);

            $user = User::create([
                'tenant_id' => $tenant->id,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => UserRole::ADMIN,
            ]);

            $token = $user->createToken('recru-hr-token')->plainTextToken;

            DB::commit();

            $this->logger->info('Successfully registered tenant with user', [
                'tenant' => $tenant,
                'user' => $user,
            ]);

            return new JsonResponse([
                'user' => $user,
                'tenant' => $user->tenant,
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
