<?php

declare(strict_types=1);

namespace App\Ai\Model;

use Illuminate\Database\Eloquent\Model;

class Embedding extends Model
{
    protected $fillable = [
        'chunk',
        'embedding',
        
    ];
}
