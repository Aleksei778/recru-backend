<?php

declare(strict_types=1);

namespace App\Ai\Tts\Providers;

use App\Ai\Tts\Dto\Result;

interface TtsInterface
{
    /**
     * Synthesize text to audio.
     *
     * @param string $text
     * @return Result|null
     */
    public function synthesize(string $text): ?Result;
}
