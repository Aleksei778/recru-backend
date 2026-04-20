<?php

declare(strict_types=1);

namespace App\Interview\Models;

use App\Candidate\Models\Candidate;
use App\Interview\Enum\Status;
use App\Vacancy\Models\Vacancy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Interview extends Model
{
    protected $fillable = [
        'candidate_id',
        'vacancy_id',
        'status',
        'token',
        'token_expires_at',
        'grade',
        'text_grade',
        'additional_info',
    ];

    protected $casts = [
        'status' => Status::class,
        'token_expires_at' => 'datetime',
    ];

    public function candidate(): belongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function vacancy(): BelongsTo
    {
        return $this->belongsTo(Vacancy::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function isPending(): bool
    {
        return $this->status === Status::PENDING;
    }

    public function isQuestionsReview(): bool
    {
        return $this->status === Status::QuestionsReview;
    }

    public function isReady(): bool
    {
        return $this->status === Status::Ready;
    }

    public function isInProgress(): bool
    {
        return $this->status === Status::InProgress;
    }

    public function isProcessing(): bool
    {
        return $this->status === Status::Processing;
    }

    public function markAsSynthesizing(): void
    {
        $this->update([
            'status' => Status::Synthesizing,
        ]);
    }

    public function markAsInProgress(): void
    {
        $this->update(['status' => Status::InProgress]);
    }

    public function markAsProcessing(): void
    {
        $this->update(['status' => Status::Processing]);
    }

    public function markAsEvaluating(): void
    {
        $this->update(['status' => Status::Evaluating]);
    }
}
