<?php

declare(strict_types=1);

namespace App\Candidate\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class Resource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'middle_name' => $this->middle_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'resume_url' => $this->resume_url,
            'linkedin_url' => $this->linkedin_url,
            'github_url' => $this->github_url,
            'source' => $this->source,
            'status' => $this->status,
            'experience_years' => $this->experience_years,
            'grade' => $this->grade,
            'education_level' => $this->education_level?->value,
            'added_by_id' => $this->added_by_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'interviews' => $this->whenLoaded('interviews'),
            'added_by' => $this->whenLoaded('addedBy'),
            'tenant' => $this->whenLoaded('tenant'),
        ];
    }
}
