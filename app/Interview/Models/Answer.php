<?php

declare(strict_types=1);

namespace App\Interview\Models;

use App\VoiceLog\Models\VoiceLog;
use Database\Factories\AnswerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

final class Answer extends Model
{
    /** @use HasFactory<AnswerFactory> */
    use HasFactory;

    public static function newFactory(): AnswerFactory
    {
        return AnswerFactory::new();
    }

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
        return $this->morphOne(VoiceLog::class, 'subject');
    }

    public function setText(string $text): void
    {
        $this->update(['text' => $text]);
    }
}
