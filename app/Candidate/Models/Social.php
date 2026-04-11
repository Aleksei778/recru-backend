<?php

declare(strict_types=1);

namespace App\Candidate\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Social extends Model
{
    protected $table = 'socials';

    protected $fillable = [
        'candidate_id',
        'name',
        'url',
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }
}
