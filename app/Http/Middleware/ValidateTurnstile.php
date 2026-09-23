<?php

namespace App\Http\Middleware;

use App\Services\TurnstileValidator;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ValidateTurnstile
{
    public function __construct(private readonly TurnstileValidator $validator) {}

    public function handle(Request $request, Closure $next, string $action): Response
    {
        if (! config('services.turnstile.enabled')) {
            return $next($request);
        }

        $token = (string) $request->input('cf-turnstile-response');

        try {
            $valid = $this->validator->verify($token, $request->ip(), $action);
        } catch (Throwable $exception) {
            Log::error('Turnstile verification service failed.', [
                'action' => $action,
                'exception' => $exception,
            ]);

            throw ValidationException::withMessages([
                'turnstile' => 'Security verification is temporarily unavailable. Please try again shortly.',
            ]);
        }

        if (! $valid) {
            Log::notice('Turnstile rejected a form submission.', [
                'action' => $action,
                'ip' => $request->ip(),
            ]);

            throw ValidationException::withMessages([
                'turnstile' => 'Please complete the security verification and submit the form again.',
            ]);
        }

        return $next($request);
    }
}
