<?php

declare(strict_types=1);

namespace App\Interview\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Answer extends Model
{
    protected $fillable = [
        'question_id',
        'text',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function voiceLog(): MorphOne
    {
        return $this->morphOne(VoiceLog::class, 'voiceable');
    }
}
