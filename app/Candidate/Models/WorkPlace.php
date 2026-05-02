<?php

declare(strict_types=1);

namespace App\Candidate\Models;

use Database\Factories\WorkPlaceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class WorkPlace extends Model
{
    /** @use HasFactory<WorkPlaceFactory> */
    use HasFactory;

    protected static function newFactory(): WorkPlaceFactory
    {
        return WorkPlaceFactory::new();
    }

    protected $table = 'workplaces';

    protected $fillable = [
        'candidate_id',
        'company_name',
        'position',
        'description',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'started_at' => 'date',
        'ended_at' => 'date',
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }
}
