<?php

declare(strict_types=1);

namespace App\Email\Repositories;

use App\Email\Models\Email;
use Illuminate\Pagination\LengthAwarePaginator;

final readonly class Repository
{
    public function findInboxWithPaginate(): LengthAwarePaginator
    {
        return Email::where('sender_id', request()->user()->id)
            ->with(['interview', 'sender'])
            ->latest()
            ->paginate(15);
    }

    public function findSentWithPaginate(): LengthAwarePaginator
    {
        return Email::where('recipient_id', request()->user()->id)
            ->with(['interview', 'recipient'])
            ->latest()
            ->paginate(15);
    }
}
