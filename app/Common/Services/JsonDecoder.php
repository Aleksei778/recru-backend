<?php

declare(strict_types=1);

namespace App\Common\Services;

final readonly class JsonDecoder
{
    public function decodeJson(string $text): mixed
    {
        $text = $this->markdownClean(trim($text));

        try {
            return json_decode($text, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return null;
        }
    }

    private function markdownClean(string $text): string
    {
        if (preg_match('/^```(?:json)?\s*(.*?)\s*```$/s', $text, $matches)) {
            return trim($matches[1]);
        }

        $start = strpos($text, '{');
        $end = strrpos($text, '}');
        if ($start !== false && $end !== false && $end > $start) {
            return substr($text, $start, $end - $start + 1);
        }

        return $text;
    }
}
