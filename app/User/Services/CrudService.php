<?php

declare(strict_types=1);

namespace App\User\Services;

use App\Tenant\Models\Tenant;
use App\User\Enum\UserRole;
use App\User\Models\User;

final readonly class CrudService
{
    public function create(
        Tenant $tenant,
        string $email,
        string $password,
        UserRole $role,
    ): User {
        return User::create([
            'tenant_id' => $tenant->id,
            'email' => $email,
            'password' => $password,
            'role' => $role,
        ]);
    }
}
