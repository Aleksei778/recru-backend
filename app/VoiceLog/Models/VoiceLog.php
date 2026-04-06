<?php

declare(strict_types=1);

namespace App\VoiceLog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class VoiceLog extends Model
{
    protected $fillable = [
        'voiceable_id',
        'voiceable_type',
        'duration',
        'size',
        'status',
        'mimetype',
        'yandex_id',
        'raw_response',
        'try_count',
    ];

    public function voiceable(): MorphTo
    {
        return $this->morphTo();
    }
}
