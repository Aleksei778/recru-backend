<?php

declare(strict_types=1);

namespace App\Email\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

final class Collection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return Resource::make($this->collection)->toArray($request);
    }
}