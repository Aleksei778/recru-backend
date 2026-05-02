<?php

declare(strict_types=1);

namespace App\Interview\Http\Resources\Question;

use Illuminate\Http\Resources\Json\ResourceCollection;

final class Collection extends ResourceCollection
{
    public static $wrap = null;

    public $collects = Resource::class;
}
