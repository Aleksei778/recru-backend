<?php

declare(strict_types=1);

namespace App\Job\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Location extends Model
{
    protected $table = 'job_locations';

    protected $fillable = [
        'tenant_id',
        'name',
        'city',
        'country',
        'job_id',
        'state',
        'address',
        'timezone',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }
}
