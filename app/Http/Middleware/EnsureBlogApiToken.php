<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBlogApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $configuredToken = (string) config('services.blog_api.token');

        if ($configuredToken === '') {
            return new JsonResponse([
                'message' => 'The blog publishing API is not configured.',
            ], 503);
        }

        $providedToken = (string) ($request->bearerToken() ?: $request->header('X-Blog-Token'));

        if ($providedToken === '' || ! hash_equals($configuredToken, $providedToken)) {
            return new JsonResponse(['message' => 'Invalid or missing API token.'], 401);
        }

        return $next($request);
    }
}
