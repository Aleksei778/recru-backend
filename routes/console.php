<?php

declare(strict_types=1);

use App\Ai\Yandex\Jobs\Check;
use Illuminate\Support\Facades\Schedule;

Schedule::job(Check::class)->everyTenSeconds();
