<?php

declare(strict_types=1);

namespace App\Interview\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Question extends Model
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
}
