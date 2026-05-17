<?php

declare(strict_types=1);

namespace App\Email\Services;

use App\Email\Dto\Create;
use App\Email\Models\Email;

final readonly class CrudService
{
    public function create(Create $dto): Email
    {
        $email = new Email($dto->toArray());

        $email->save();

        return $email;
    }
}
