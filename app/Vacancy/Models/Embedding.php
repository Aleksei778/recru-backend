<?php

declare(strict_types=1);

namespace App\Vacancy\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Embedding extends Model
{
    protected $fillable = [
        'vacancy_id',
        'chunk',
        'embedding',
    ];

    public function vacancy(): BelongsTo
    {
        return $this->belongsTo(Vacancy::class);
    }
}
