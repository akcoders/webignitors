<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return to_route('admin.login');
        }

        abort_unless($request->user()->is_admin, 403, 'Administrator access is required.');

        return $next($request);
    }
}
