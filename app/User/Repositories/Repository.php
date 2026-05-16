<?php

declare(strict_types=1);

namespace App\User\Repositories;

use App\User\Enum\UserRole;
use App\User\Models\User;

final readonly class Repository
{
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * @return User[]
     */
    public function findHr(): array
    {
        return User::where('role', UserRole::HR)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }
}
