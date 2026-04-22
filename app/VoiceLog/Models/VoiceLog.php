<?php

declare(strict_types=1);

namespace App\VoiceLog\Models;

use App\VoiceLog\Enum\Type;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class VoiceLog extends Model
{
    protected $fillable = [
        'subject_id',
        'subject_type',
        'audio_path',
        'type',
        'mime_type',
    ];

    protected $casts = [
        'type' => Type::class,
    ];

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
