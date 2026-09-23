<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        RateLimiter::for('registration', function (Request $request): array {
            $email = Str::lower(trim((string) $request->input('email')));
            $response = static fn (Request $request, array $headers) => back()
                ->withInput($request->only('name', 'email'))
                ->withErrors([
                    'email' => 'Too many registration attempts. Please wait one minute and try again.',
                ])
                ->withHeaders($headers);

            return [
                Limit::perMinute(10)
                    ->by('email:'.$email)
                    ->response($response),
                Limit::perHour(60)
                    ->by('ip:'.$request->ip())
                    ->response($response),
            ];
        });
    }
}
