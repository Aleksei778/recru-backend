<?php

declare(strict_types=1);

namespace App\Job\Models;

use App\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class Job extends Model
{
    protected $table = 'jobs';

    protected $fillable = [
        'title',
        'description',
        'department_id',
        'employment_type',
        'work_mode',
        'seniority_level',
        'experience_years_min',
        'experience_years_max',
        'salary_min',
        'salary_max',
        'salary_currency',
        'status',
        'published_at',
        'closed_at',
        'hiring_by_id',
        'created_by_id',
    ];

    public function requirements(): HasMany
    {
        return $this->hasMany(Requirement::class);
    }

    public function location(): HasOne
    {
        return $this->hasOne(Location::class);
    }

    public function responsibilities(): HasMany
    {
        return $this->hasMany(Responsibility::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function hiringBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hiring_by_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
