<?php

declare(strict_types=1);

namespace App\Interview\Http\Resources;

use App\Interview\Models\Interview;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Interview
 */
final class Resource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'status' => $this->status,
            'token' => $this->token,
            'token_expires_at' => $this->token_expires_at,
            'grade' => $this->grade,
            'text_grade' => $this->text_grade,
            'additional_info' => $this->additional_info,
            'candidate' => $this->whenLoaded('candidate', function () {
                return \App\Candidate\Http\Resources\Resource::make($this->candidate);
            }),
            'vacancy' => $this->whenLoaded('vacancy', function () {
                return \App\Vacancy\Http\Resources\Resource::make($this->vacancy);
            }),
        ];
    }
}
