<?php

declare(strict_types=1);

namespace App\Email\Models;

use App\Common\Enum\Locale;
use App\User\Models\User;
use App\Interview\Models\Interview;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Email extends Model
{
    protected $table = 'emails';

    protected $fillable = [
        'user_id',
        'interview_id',
        'locale',
    ];

    protected $casts = [
        'locale' => Locale::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function interview(): BelongsTo
    {
        return $this->belongsTo(Interview::class);
    }
}
