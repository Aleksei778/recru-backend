<?php

declare(strict_types=1);

namespace App\Skill\Traits;

use App\Skill\Models\Skill;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasSkills
{
    public function skills(): MorphToMany
    {
        return $this->morphToMany(Skill::class, 'skillable');
    }
}
