<?php

declare(strict_types=1);

namespace App\Candidate\Http\Resources;

use App\Candidate\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Candidate\Http\Resources\{
    WorkPlace\Collection as WorkPlaceCollection,
    Social\Collection as SocialCollection,
};
use App\Interview\Http\Resources\{Collection as InterviewCollection};
use App\Skill\Http\Resources\{Collection as SkillCollection};

/**
 * @mixin Candidate
 */
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
            'source' => $this->source?->value,
            'status' => $this->status?->value,
            'experience_years' => $this->experience_years,
            'education_level' => $this->education_level?->value,
            'grade' => $this->grade?->value,
            'interviews' => $this->whenLoaded('interviews', function () {
                return new InterviewCollection($this->interviews);
            }),
            'workplaces' => $this->whenLoaded('workPlaces', function () {
                return new WorkPlaceCollection($this->workPlaces);
            }),
            'socials' => $this->whenLoaded('socials', function () {
                return new SocialCollection($this->socials);
            }),
            'skills' => $this->whenLoaded('skills', function () {
                return new SkillCollection($this->skills);
            }),
        ];
    }
}
