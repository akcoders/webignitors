<?php

namespace Tests\Unit;

use App\Services\WebsiteAudit\SafeWebsiteUrl;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SafeWebsiteUrlTest extends TestCase
{
    public function test_it_normalises_public_website_urls(): void
    {
        $service = app(SafeWebsiteUrl::class);

        $this->assertSame('https://example.com/', $service->normalize('example.com'));
        $this->assertSame(
            'https://example.com/products?category=apps',
            $service->normalize('HTTPS://Example.com/products?category=apps')
        );
    }

    #[DataProvider('unsafeUrls')]
    public function test_it_blocks_unsafe_network_targets(string $url): void
    {
        $this->expectException(InvalidArgumentException::class);

        app(SafeWebsiteUrl::class)->assertPublic($url);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function unsafeUrls(): array
    {
        return [
            'localhost' => ['http://localhost'],
            'local development domain' => ['https://website.local'],
            'loopback address' => ['http://127.0.0.1'],
            'private address' => ['http://10.0.0.1'],
            'credentials' => ['https://user:password@example.com'],
            'custom port' => ['https://example.com:8080'],
        ];
    }
}
