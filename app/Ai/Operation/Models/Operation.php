<?php

declare(strict_types=1);

namespace App\Ai\Operation\Models;

use App\Tenant\Traits\BelongsToTenant;
use Database\Factories\OperationFactory;
use App\Ai\Operation\Enum\{Status, Type};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class Operation extends Model
{
    /** @use HasFactory<OperationFactory> */
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'subject_id',
        'subject_type',
        'type',
        'tenant_id',
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

    public function markAsCompleted(
        array $rawResponse,
        string $result,
    ): void {
        $this->update([
            'status' => Status::Completed,
            'raw_response' => $rawResponse,
            'result' => $result,
        ]);
    }

    public function markAsFailed(array $rawResponse): void
    {
        $this->update([
            'status' => Status::Failed,
            'raw_response' => $rawResponse,
        ]);
    }
}
