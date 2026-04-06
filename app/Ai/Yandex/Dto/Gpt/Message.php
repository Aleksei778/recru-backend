<?php

declare(strict_types=1);

namespace App\Ai\Yandex\Dto\Gpt;

final readonly class Message
{
    public function __construct(
        public string $role,
        public string $text,
    ) {
    }

    public function toArray(): array
    {
        return [
            'role' => $this->role,
            'text' => $this->text,
        ];
    }
}
