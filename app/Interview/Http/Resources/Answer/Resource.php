<?php

declare(strict_types=1);

namespace App\Interview\Http\Resources\Answer;

use App\Interview\Models\Question;
use Illuminate\Http\Resources\Json\JsonResource;

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
        ];
    }
}
