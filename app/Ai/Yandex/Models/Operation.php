<?php

declare(strict_types=1);

namespace App\Ai\Yandex\Models;

use App\Ai\Yandex\Enum\{OperationStatus, OperationType};
use App\Interview\Models\Interview;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Operation extends Model
{
    protected $fillable = [
        'interview_id',
        'type',
        'status',
        'yandex_id',
        'raw_request',
        'raw_response',
        'result',
    ];

    protected $casts = [
        'type' => OperationType::class,
        'status' => OperationStatus::class,
        'raw_request' => 'array',
        'raw_response' => 'array',
        'result' => 'array',
    ];

    public function interview(): BelongsTo
    {
        return $this->belongsTo(Interview::class);
    }
}
