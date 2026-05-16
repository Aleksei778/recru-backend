<?php

declare(strict_types=1);

namespace App\Common\Http\Middleware;

use App\User\Enum\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->role !== UserRole::ADMIN) {
            abort(Response::HTTP_FORBIDDEN, 'Access denied.');
        }

        return $next($request);
    }
}
