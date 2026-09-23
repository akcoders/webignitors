<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class TurnstileValidator
{
    public function verify(string $token, ?string $ipAddress, string $expectedAction): bool
    {
        $secret = (string) config('services.turnstile.secret_key');

        if ($secret === '') {
            throw new RuntimeException('Cloudflare Turnstile is enabled but its secret key is missing.');
        }

        if ($token === '' || mb_strlen($token) > 2048) {
            return false;
        }

        $response = Http::asForm()
            ->acceptJson()
            ->timeout(config('services.turnstile.timeout', 10))
            ->post(config('services.turnstile.verify_url'), [
                'secret' => $secret,
                'response' => $token,
                'remoteip' => $ipAddress,
                'idempotency_key' => (string) Str::uuid(),
            ]);

        if (! $response->successful()) {
            throw new RuntimeException("Turnstile Siteverify returned HTTP {$response->status()}.");
        }

        $result = $response->json();
        if (! is_array($result) || ! ($result['success'] ?? false)) {
            return false;
        }

        if (($result['action'] ?? null) !== $expectedAction) {
            return false;
        }

        $allowedHostnames = array_values(array_filter(array_map(
            static fn (string $hostname): string => strtolower(trim($hostname)),
            explode(',', (string) config('services.turnstile.allowed_hostname'))
        )));
        $responseHostname = strtolower((string) ($result['hostname'] ?? ''));

        return $allowedHostnames === [] || collect($allowedHostnames)
            ->contains(fn (string $hostname): bool => hash_equals($hostname, $responseHostname));
    }
}
