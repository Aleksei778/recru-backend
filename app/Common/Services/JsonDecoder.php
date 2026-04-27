<?php

declare(strict_types=1);

namespace App\Common\Services;

final readonly class JsonDecoder
{
    public function decodeJson(string $text): mixed
    {
        $text = trim($text);
        $this->markdownClean($text);

        try {
            return json_decode($text, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return null;
        }
    }

    private function markdownClean(string &$text): void
    {
        if (preg_match('/^```json\s+(.*?)\s+```$/s', $text, $matches)) {
            $text = $matches[1];
        }
    }
}
