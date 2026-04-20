<?php

declare(strict_types=1);

namespace App\Ai\Tts\Contracts;

use App\Ai\Tts\Dto\Result;

interface SyncInterface
{
    /**
     * Synthesize text to audio.
     *
     * @param string $text
     * @return Result|null
     */
    public function synthesize(string $text): ?Result;
}
