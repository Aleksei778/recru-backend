<?php

declare(strict_types=1);

namespace App\User\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

final class Collection extends ResourceCollection
{
    public $collects = Resource::class;
}
