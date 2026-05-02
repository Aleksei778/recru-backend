<?php

declare(strict_types=1);

namespace App\Interview\Http\Resources\Question;

use App\Interview\Models\Question;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Interview\Http\Resources\Answer\{Resource as AnswerResource};

/**
 * @mixin Question
 */
final class Resource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'text' => $this->text,
            'number' => $this->number,
            'answer' => $this->whenLoaded('answer', function () {
                return new AnswerResource($this->answer);
            })
        ];
    }
}
