<?php

declare(strict_types=1);

namespace App\Tenant\Http\Resources;

use App\Tenant\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Tenant
 */
final class Resource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'website' => $this->website,
            'industry' => $this->industry,
            'subdomain' => $this->subdomain,
            'created_at' => $this->created_at,
        ];
    }
}
