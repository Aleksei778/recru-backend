<?php

declare(strict_types=1);

namespace App\Resume\Models;

use App\Candidate\Models\Candidate;
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
        'summary',
        'score',
    ];

    protected $casts = [
        'parsed_data' => 'array',
        'size' => 'integer',
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }
}
