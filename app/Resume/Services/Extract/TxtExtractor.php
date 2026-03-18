<?php

declare(strict_types=1);

namespace App\Resume\Services\Extract;

use Illuminate\Http\UploadedFile;

final readonly class TxtExtractor
{
    public function extract(UploadedFile $file): string
    {
        return file_get_contents($file->getRealPath());
    }
}
