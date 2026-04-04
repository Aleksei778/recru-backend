<?php

declare(strict_types=1);

namespace App\Email\Models;

use App\Candidate\Models\Candidate;
use App\User\Models\User;
use App\Vacancy\Models\Vacancy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Email extends Model
{
    protected $table = 'emails';

    protected $fillable = [
        'user_id',
        'candidate_id',
        'vacancy_id',
        'title',
        'body',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function vacancy(): BelongsTo
    {
        return $this->belongsTo(Vacancy::class);
    }
}
