<?php

declare(strict_types=1);

namespace App\Candidate\Models;

use App\Candidate\Enum\{EducationLevel, Grade, Source, Status};
use App\Tenant\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use App\User\Models\User;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

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
        'grade',
        'status',
        'experience_years',
        'education_level',
        'added_by_id',
    ];

    protected $casts = [
        'education_level' => EducationLevel::class,
        'status' => Status::class,
        'source' => Source::class,
        'grade' => Grade::class,
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by_id');
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }
}
