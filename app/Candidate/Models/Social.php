<?php

declare(strict_types=1);

namespace App\Candidate\Models;

use Database\Factories\SocialFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Social extends Model
{
    /** @use HasFactory<SocialFactory> */
    use HasFactory;

    protected static function newFactory(): SocialFactory
    {
        return SocialFactory::new();
    }

    protected $table = 'socials';

    protected $fillable = [
        'candidate_id',
        'name',
        'url',
    ];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }
}
