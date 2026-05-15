<?php

declare(strict_types=1);

namespace App\Vacancy\Models;

use App\Candidate\Enum\EducationLevel;
use App\Candidate\Enum\Grade;
use App\Interview\Models\Interview;
use App\Skill\Traits\HasSkills;
use App\Tenant\Models\Tenant;
use App\Tenant\Traits\BelongsToTenant;
use Auth;
use Database\Factories\VacancyFactory;
use App\Vacancy\Enum\{EmploymentType, Status, WorkMode};
use App\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

final class Vacancy extends Model
{
    /** @use HasFactory<VacancyFactory> */
    use HasFactory, HasSkills, BelongsToTenant;

    protected static function newFactory(): VacancyFactory
    {
        return VacancyFactory::new();
    }

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
        'grade',
        'experience_years',
        'education_level',
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
        'grade' => Grade::class,
        'education_level' => EducationLevel::class,
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }

    public function isDraft(): bool
    {
        return $this->status === Status::DRAFT;
    }

    public function isPublished(): bool
    {
        return $this->status === Status::PUBLISHED;
    }

    public function setCreatedByAttribute(): void
    {
        $this->attributes['created_by_id'] = Auth::id();
    }

    public function setPublishedAtAttribute(): void
    {
        $this->attributes['published_at'] = now();
    }

    public function setClosedAtAttribute(): void
    {
        $this->attributes['closed_at'] = null;
    }
}
