<?php

declare(strict_types=1);

namespace App\Job\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Requirement extends Model
{
    protected $table = 'job_requirements';

    protected $fillable = [
        'description',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }
}
