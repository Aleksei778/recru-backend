<?php

declare(strict_types=1);

namespace App\User\Services;

use App\User\Models\User;

final readonly class TokenService
{
    public function generate(User $user): string
    {
        return $user->createToken('recru-hr-token')->plainTextToken;
    }

    public function dropAll(User $user): void
    {
        $user->tokens()->delete();
    }

    public function dropCurrent(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
