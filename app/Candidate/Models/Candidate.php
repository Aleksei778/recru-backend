<?php

declare(strict_types=1);

namespace App\Candidate\Models;

use App\Candidate\Enum\EducationLevel;
use App\Tenant\Models\Tenant;
use App\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class Candidate extends Model
{
    protected $table = 'candidates';

    protected $fillable = [
        'tenant_id',
        'first_name',
        'last_name',
        'middle_name',
        'email',
        'phone',
        'resume_url',
        'linkedin_url',
        'github_url',
        'source',
        'status',
        'experience_years',
        'education_level',
        'added_by_id',
        'match_score', // 0-100 (ai predicted)
    ];

    protected $casts = [
        'education_level' => EducationLevel::class,
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by_id');
    }

    public function interview(): HasOne
    {
        return $this->hasOne(Interview::class);
    }
}
