<?php

declare(strict_types=1);

namespace App\Common\Services;

use Illuminate\Support\Facades\Storage as StorageFacade;

final readonly class Storage
{
    public function put(
        string $disk,
        string $path,
        string $content,
    ): void {
        StorageFacade::disk($disk)->put($path, $content);
    }

    public function url(string $disk, string $path): string
    {
        return StorageFacade::disk($disk)->url($path);
    }
}
