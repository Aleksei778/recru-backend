<?php

declare(strict_types=1);

namespace App\Vacancy\Models;

use App\Interview\Models\Interview;
use App\Skill\Traits\HasSkills;
use App\Tenant\Models\Tenant;
use App\Vacancy\Enum\{EmploymentType, Status, WorkMode};
use App\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Vacancy extends Model
{
    use HasSkills;

    protected $table = 'vacancies';

    protected $fillable = [
        'tenant_id',
        'title',
        'description',
        'employment_type',
        'work_mode',
        'salary_min',
        'salary_max',
        'salary_currency',
        'status',
        'location',
        'published_at',
        'closed_at',
        'created_by_id',
    ];

    protected $casts = [
        'employment_type' => EmploymentType::class,
        'work_mode' => WorkMode::class,
        'status' => Status::class,
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function embeddings(): HasMany
    {
        return $this->hasMany(Embedding::class);
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }
}
