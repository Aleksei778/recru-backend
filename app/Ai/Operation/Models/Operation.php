<?php

declare(strict_types=1);

namespace App\Ai\Operation\Models;

use App\Ai\Operation\Enum\{Status, Type};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, MorphTo};

final class Operation extends Model
{
    protected $fillable = [
        'subject_id',
        'subject_type',
        'type',
        'status',
        'provider_id',
        'provider',
        'raw_request',
        'raw_response',
        'result',
    ];

    protected $casts = [
        'type' => Type::class,
        'status' => Status::class,
        'raw_request' => 'array',
        'raw_response' => 'array',
    ];

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function isCompleted(): bool
    {
        return $this->status === Status::Completed;
    }
}
