<?php

namespace App\Services\WebsiteAudit;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class WebsiteFetcher
{
    public function __construct(private readonly SafeWebsiteUrl $safeUrl) {}

    /**
     * @return array{url: string, status: int, headers: array<string, mixed>, body: string}
     */
    public function fetch(string $url): array
    {
        $currentUrl = $this->safeUrl->normalize($url);

        for ($redirects = 0; $redirects <= 4; $redirects++) {
            $this->safeUrl->assertPublic($currentUrl);

            $response = Http::withHeaders([
                'User-Agent' => config('audit.user_agent'),
                'Accept' => 'text/html,application/xhtml+xml',
            ])
                ->timeout(30)
                ->connectTimeout(10)
                ->withOptions(['allow_redirects' => false])
                ->get($currentUrl);

            if ($response->redirect()) {
                $location = $response->header('Location');
                if (! $location) {
                    throw new RuntimeException('The website returned an invalid redirect.');
                }

                $currentUrl = $this->resolveRedirect($currentUrl, $location);

                continue;
            }

            $this->ensureHtmlResponse($response);
            $body = $response->body();

            if (strlen($body) > config('audit.max_html_bytes')) {
                throw new RuntimeException('The website HTML is too large to audit safely.');
            }

            return [
                'url' => $currentUrl,
                'status' => $response->status(),
                'headers' => $response->headers(),
                'body' => $body,
            ];
        }

        throw new RuntimeException('The website redirected too many times.');
    }

    private function ensureHtmlResponse(Response $response): void
    {
        if (! $response->successful()) {
            throw new RuntimeException("The website returned HTTP {$response->status()}.");
        }

        $contentType = strtolower($response->header('Content-Type', ''));
        if ($contentType !== '' && ! str_contains($contentType, 'text/html') && ! str_contains($contentType, 'application/xhtml')) {
            throw new RuntimeException('The submitted URL is not an HTML web page.');
        }
    }

    private function resolveRedirect(string $baseUrl, string $location): string
    {
        if (preg_match('#^https?://#i', $location)) {
            return $this->safeUrl->normalize($location);
        }

        $parts = parse_url($baseUrl);
        $origin = ($parts['scheme'] ?? 'https').'://'.($parts['host'] ?? '');
        if (isset($parts['port'])) {
            $origin .= ':'.$parts['port'];
        }

        if (str_starts_with($location, '//')) {
            return $this->safeUrl->normalize(($parts['scheme'] ?? 'https').':'.$location);
        }

        if (str_starts_with($location, '/')) {
            return $this->safeUrl->normalize($origin.$location);
        }

        $basePath = $parts['path'] ?? '/';
        $directory = rtrim(str_replace('\\', '/', dirname($basePath)), '/');

        return $this->safeUrl->normalize($origin.($directory ? '/'.$directory : '').'/'.$location);
    }
}
