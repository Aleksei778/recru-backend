<?php

declare(strict_types=1);

namespace App\Email\Dto;

use App\Common\Enum\Locale;
use App\Email\Enum\Type;
use App\Interview\Models\Interview;

final readonly class Create
{
    public function __construct(
        public ?int $senderId,
        public Interview $interview,
        public Type $type,
        public string $subject,
        public string $body,
        public int $recipientId,
        public string $recipientType,
        public Locale $locale,
    ) {
    }

    public function toArray(): array
    {
        return [
            'sender_id' => $this->senderId,
            'interview_id' => $this->interview->id,
            'type' => $this->type,
            'subject' => $this->subject,
            'body' => $this->body,
            'recipient_id' => $this->recipientId,
            'recipient_type' => $this->recipientType,
            'locale' => $this->locale,
        ];
    }
}
