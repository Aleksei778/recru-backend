<?php

declare(strict_types=1);

namespace App\Candidate\Models;

use App\Common\Enum\Locale;
use App\Interview\Models\Interview;
use App\Resume\Models\Resume;
use App\Skill\Traits\HasSkills;
use Database\Factories\CandidateFactory;
use App\Candidate\Enum\{EducationLevel, Source, Status, Grade};
use App\Tenant\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User\Models\User;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

final class Candidate extends Model
{
    /** @use HasFactory<CandidateFactory> */
    use HasFactory, HasSkills;

    protected static function newFactory(): CandidateFactory
    {
        return CandidateFactory::new();
    }

    protected $table = 'candidates';

    protected $fillable = [
        'tenant_id',
        'first_name',
        'last_name',
        'middle_name',
        'email',
        'phone',
        'source',
        'grade',
        'status',
        'experience_years',
        'education_level',
        'added_by_id',
        'locale',
    ];

    protected $casts = [
        'grade' => Grade::class,
        'education_level' => EducationLevel::class,
        'status' => Status::class,
        'source' => Source::class,
        'locale' => Locale::class,
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

    public function workPlaces(): HasMany
    {
        return $this->hasMany(WorkPlace::class);
    }

    public function socials(): HasMany
    {
        return $this->hasMany(Social::class);
    }

    public function resumes(): HasMany
    {
        return $this->hasMany(Resume::class);
    }
}
