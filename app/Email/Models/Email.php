<?php

declare(strict_types=1);

namespace App\Email\Models;

use App\Common\Enum\Locale;
use Database\Factories\EmailFactory;
use App\Email\Enum\Type;
use App\User\Models\User;
use App\Interview\Models\Interview;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class Email extends Model
{
    /** @use HasFactory<EmailFactory> */
    use HasFactory;

    protected static function newFactory(): EmailFactory
    {
        return EmailFactory::new();
    }

    protected $table = 'emails';

    protected $fillable = [
        'interview_id',
        'sender_id',
        'recipient_type',
        'recipient_id',
        'type',
        'locale',
        'subject',
        'body',
        'sent_at',
    ];

    protected $casts = [
        'locale' => Locale::class,
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
