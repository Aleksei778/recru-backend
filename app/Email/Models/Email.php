<?php

declare(strict_types=1);

namespace App\Email\Models;

use App\Common\Enum\Locale;
use App\Email\Enum\{Status, Type};
use App\User\Models\User;
use App\Interview\Models\Interview;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class Email extends Model
{
    protected $table = 'emails';

    protected $fillable = [
        'interview_id',
        'sender_id',
        'recipient_type',
        'recipient_id',
        'status',
        'type',
        'locale',
        'sent_at',
    ];

    protected $casts = [
        'locale' => Locale::class,
        'status' => Status::class,
        'type' => Type::class,
    ];

    public function recipient(): MorphTo
    {
        return $this->morphTo();
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function interview(): BelongsTo
    {
        return $this->belongsTo(Interview::class);
    }
}
