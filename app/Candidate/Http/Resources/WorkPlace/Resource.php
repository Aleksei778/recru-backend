<?php

declare(strict_types=1);

namespace App\Candidate\Http\Resources\WorkPlace;

use App\Candidate\Models\WorkPlace;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin WorkPlace
 */
final class Resource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company_name' => $this->company_name,
            'position' => $this->position,
            'description' => $this->description,
            'started_at' => $this->started_at,
            'ended_at' => $this->ended_at,
        ];
    }
}
