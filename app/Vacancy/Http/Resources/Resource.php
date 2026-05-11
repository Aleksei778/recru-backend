<?php

declare(strict_types=1);

namespace App\Vacancy\Http\Resources;

use App\Skill\Http\Resources\Collection as SkillCollection;
use App\Vacancy\Models\Vacancy;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Vacancy
 */
final class Resource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'title' => $this->title,
            'description' => $this->description,
            'employment_type' => $this->employment_type?->value,
            'work_mode' => $this->work_mode?->value,
            'salary_min' => $this->salary_min,
            'salary_max' => $this->salary_max,
            'salary_currency' => $this->salary_currency,
            'status' => $this->status?->value,
            'location' => $this->location,
            'published_at' => $this->published_at,
            'experience_years' => $this->experience_years,
            'closed_at' => $this->closed_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'grade' => $this->grade,
            'skills' => $this->whenLoaded('skills', function () {
                return new SkillCollection($this->skills);
            }),
        ];
    }
}
