<?php

namespace App\Skill\Models;

use App\Candidate\Models\Candidate;
use App\Vacancy\Models\Vacancy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

final class Skill extends Model
{
    protected $fillable = ['name', 'slug'];

    public function vacancies(): MorphToMany
    {
        return $this->morphedByMany(Vacancy::class, 'skillable');
    }

    public function candidates(): MorphToMany
    {
        return $this->morphedByMany(Candidate::class, 'skillable');
    }
}
