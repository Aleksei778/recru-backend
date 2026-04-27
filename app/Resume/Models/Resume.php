<?php

declare(strict_types=1);

namespace App\Resume\Models;

use App\Candidate\Models\Candidate;
use App\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Resume extends Model
{
    protected $fillable = [
        'candidate_id',
        'file_path',
        'file_name',
        'mime_type',
        'size',
        'storage_disk',
        'parsed_data',
        'text_grade',
        'grade',
        'saved_by_id',
    ];

    protected $casts = [
        'parsed_data' => 'array',
        'size' => 'integer',
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function savedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'saved_by_id');
    }

    public function setGrades(
        int $grade,
        string $textGrade,
    ): void {
        $this->update([
            'grade' => $grade,
            'text_grade' => $textGrade,
        ]);
    }

    public function setCandidateId(int $candidateId): void
    {
        $this->update(['candidate_id' => $candidateId]);
    }
}
