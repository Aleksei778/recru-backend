<?php

declare(strict_types=1);

namespace App\User\Services;

use App\Common\Enum\Locale;
use App\Tenant\Models\Tenant;
use App\User\Enum\UserRole;
use App\User\Models\User;

final readonly class CrudService
{
    public function createAdmin(
        Tenant $tenant,
        string $email,
        string $password,
    ): User {
        return User::create([
            'tenant_id' => $tenant->id,
            'email' => $email,
            'password' => $password,
            'name' => 'Admin',
            'role' => UserRole::HR,
        ]);
    }

    public function createHr(
        Tenant $tenant,
        string $email,
        string $password,
        ?string $name,
        Locale $locale,
    ): User {
        return User::create([
            'tenant_id' => $tenant->id,
            'email' => $email,
            'password' => $password,
            'role' => UserRole::HR,
            'name' => $name,
            'locale' => $locale,
        ]);
    }

    public function delete(User $user): void
    {
        $user->delete();
    }

    public function updatePersonalData(
        User $user,
        string $email,
        ?string $name,
        Locale $locale,
    ): void {
        $user->update([
            'name' => $name,
            'locale' => $locale,
            'email' => $email,
        ]);
    }

    public function updatePassword(User $user, string $newPassword): void
    {
        $user->update([
            'password' => $newPassword,
        ]);
    }
}
