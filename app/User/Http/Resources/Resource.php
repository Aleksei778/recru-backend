<?php

declare(strict_types=1);

namespace App\User\Http\Resources;

use App\User\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
final class Resource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role->value,
            'locale' => $this->locale->value,
            'created_at' => $this->created_at,
        ];
    }
}
