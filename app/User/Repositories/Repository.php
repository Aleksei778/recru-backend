<?php

declare(strict_types=1);

namespace App\User\Repositories;

use App\User\Models\User;

final readonly class Repository
{
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
}
