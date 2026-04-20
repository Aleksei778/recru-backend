<?php

declare(strict_types=1);

namespace App\Resume\Services\Extract;

use Illuminate\Http\UploadedFile;

final readonly class TxtExtractor implements ExtractorInterface
{
    public function extract(UploadedFile $file): string
    {
        $content = file_get_contents($file->getRealPath());

        if ($content === false) {
            throw new \RuntimeException('Failed to read text file');
        }

        return preg_replace('/\s+/', ' ', trim($content));
    }
}
