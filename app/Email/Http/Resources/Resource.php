<?php

declare(strict_types=1);

namespace App\Email\Http\Resources;

use App\Email\Models\Email;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Email
 */
final class Resource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'interview_id' => $this->interview_id,
            'sender_id' => $this->sender_id,
            'sender' => $this->whenLoaded('sender', function () {
                return $this->sender;
            }),
            'recipient' => $this->whenLoaded('recipient', function () {
                return $this->recipient;
            }),
            'recipient_type' => $this->recipient_type,
            'recipient_id' => $this->recipient_id,
            'status' => $this->status?->value,
            'type' => $this->type?->value,
            'locale' => $this->locale?->value,
            'subject' => $this->subject,
            'sent_at' => $this->sent_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'interview' => \App\Interview\Http\Resources\Resource::make($this->whenLoaded('interview')),
        ];
    }
}