<?php

declare(strict_types=1);

namespace App\Interview\Http\Resources;

use App\Interview\Models\Interview;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Candidate\Http\Resources\{Resource as CandidateResource};
use App\Vacancy\Http\Resources\{Resource as VacancyResource};
use App\Interview\Http\Resources\Question\{Collection as QuestionCollection};

/**
 * @mixin Interview
 */
final class Resource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'token' => $this->token,
            'token_expires_at' => $this->token_expires_at,
            'grade' => $this->grade,
            'text_grade' => $this->text_grade,
            'candidate' => $this->whenLoaded('candidate', function () {
                return CandidateResource::make($this->candidate);
            }),
            'vacancy' => $this->whenLoaded('vacancy', function () {
                return VacancyResource::make($this->vacancy);
            }),
            'questions' => $this->whenLoaded('questions', function () {
                return QuestionCollection::make($this->questions->load('answer'));
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
