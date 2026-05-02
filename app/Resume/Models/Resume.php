<?php

declare(strict_types=1);

namespace App\Resume\Models;

use App\Candidate\Models\Candidate;
use Database\Factories\ResumeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Resume extends Model
{
    /** @use HasFactory<ResumeFactory> */
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'file_path',
        'mimetype',
        'parsed_data',
        'text_grade',
        'grade',
    ];

    protected $casts = [
        'parsed_data' => 'array',
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function setCandidateId(int $candidateId): void
    {
        $this->update(['candidate_id' => $candidateId]);
    }
}
