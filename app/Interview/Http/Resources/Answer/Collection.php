<?php

declare(strict_types=1);

namespace App\Interview\Http\Resources\Answer;

use Illuminate\Http\Resources\Json\ResourceCollection;

final class Collection extends ResourceCollection
{
    public static $wrap = null;

    public $collects = Resource::class;
}
