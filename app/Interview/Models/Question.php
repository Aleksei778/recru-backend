<?php

declare(strict_types=1);

namespace App\Interview\Models;

use App\VoiceLog\Models\VoiceLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasOne, MorphOne};

final class Question extends Model
{
    protected $fillable = [
        'interview_id',
        'text',
        'number',
    ];

    public function interview(): BelongsTo
    {
        return $this->belongsTo(Interview::class);
    }

    public function voiceLog(): MorphOne
    {
        return $this->morphOne(VoiceLog::class, 'voiceable');
    }

    public function answer(): HasOne
    {
        return $this->hasOne(Answer::class);
    }
}
