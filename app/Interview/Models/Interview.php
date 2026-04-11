<?php

declare(strict_types=1);

namespace App\Interview\Models;

use App\Candidate\Models\Candidate;
use App\Interview\Enum\Status;
use App\Vacancy\Models\Vacancy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function isPending(): bool
    {
        return $this->status === Status::PENDING;
    }

    public function markAsInProgress(): void
    {
        $this->update(['status' => Status::IN_PROGRESS]);
    }
}
