<?php

declare(strict_types=1);

namespace App\Skill\Models;

use App\Candidate\Models\Candidate;
use App\Vacancy\Models\Vacancy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, MorphToMany};

final class Skill extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'category_id',
        'aliases'
    ];

    protected $casts = [
        'aliases' => 'array'
    ];

    public function category(): belongsTo
    {
        return $this->belongsTo(SkillCategory::class);
    }

    public function vacancies(): MorphToMany
    {
        return $this->morphedByMany(Vacancy::class, 'skillable');
    }

    public function candidates(): MorphToMany
    {
        return $this->morphedByMany(Candidate::class, 'skillable');
    }
}
