<?php

declare(strict_types=1);

namespace App\Resume\Services\Extract;

use Illuminate\Http\UploadedFile;

interface ExtractorInterface
{
    public function extract(UploadedFile $file): string;
}
