<?php

namespace Tests\Feature;

use App\Jobs\ProcessWebsiteReport;
use App\Models\User;
use App\Models\WebsiteReport;
use App\Services\WebsiteAudit\WebsiteAuditRunner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class WebsiteAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_audit_page_has_the_complete_presentation(): void
    {
        $this->get('/website-audit')
            ->assertOk()
            ->assertSee('Your website,')
            ->assertSee('Eight lenses. One clear plan.')
            ->assertSee('Lighthouse')
            ->assertSee('Marketing')
            ->assertSee('Security');
    }

    public function test_guest_url_is_preserved_until_account_creation(): void
    {
        Queue::fake();

        $this->post('/website-audit', [
            'url' => 'example.com',
            'website' => '',
        ])
            ->assertRedirect(route('register'))
            ->assertSessionHas('pending_audit_url', 'https://example.com/');

        $this->post('/register', [
            'name' => 'Audit Customer',
            'email' => 'audit@example.com',
            'password' => 'Report1234',
            'password_confirmation' => 'Report1234',
            'website' => '',
        ])->assertRedirect();

        $this->assertAuthenticated();
        $this->assertDatabaseHas('website_reports', [
            'requested_url' => 'https://example.com/',
            'domain' => 'example.com',
            'status' => 'queued',
        ]);
        Queue::assertPushed(ProcessWebsiteReport::class);
    }

    public function test_authenticated_user_can_start_and_privately_view_a_report(): void
    {
        Queue::fake();
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $response = $this->actingAs($owner)->post('/website-audit', [
            'url' => 'https://example.com',
            'website' => '',
        ]);

        $report = WebsiteReport::query()->firstOrFail();
        $response->assertRedirect(route('reports.show', $report));
        Queue::assertPushed(ProcessWebsiteReport::class);

        $this->actingAs($owner)
            ->get(route('reports.show', $report))
            ->assertOk()
            ->assertSee('Analysis in progress')
            ->assertSee('data-report-status-url', false);

        $this->actingAs($otherUser)
            ->get(route('reports.show', $report))
            ->assertForbidden();

        $this->actingAs($otherUser)
            ->get(route('reports.status', $report))
            ->assertForbidden();
    }

    public function test_private_and_local_network_urls_are_rejected(): void
    {
        $this->withoutMiddleware(ThrottleRequests::class);

        foreach ([
            'http://localhost',
            'http://127.0.0.1',
            'http://10.0.0.1',
            'ftp://example.com',
            'https://example.com:8080',
        ] as $url) {
            $this->from('/website-audit')
                ->post('/website-audit', ['url' => $url, 'website' => ''])
                ->assertRedirect('/website-audit')
                ->assertSessionHasErrors('url');
        }

        $this->assertDatabaseCount('website_reports', 0);
    }

    public function test_api_pipeline_creates_detailed_mysql_records_and_branded_pdf(): void
    {
        Notification::fake();
        Storage::fake('local');
        config([
            'audit.resolve_dns' => false,
            'audit.pagespeed.enabled' => true,
            'audit.crux.enabled' => false,
            'audit.w3c.enabled' => true,
            'audit.observatory.enabled' => true,
            'audit.browserless.enabled' => false,
        ]);

        Http::fake([
            'https://example.com/*' => Http::response($this->auditedHtml(), 200, [
                'Content-Type' => 'text/html; charset=UTF-8',
                'Strict-Transport-Security' => 'max-age=31536000',
                'X-Content-Type-Options' => 'nosniff',
            ]),
            'https://www.googleapis.com/pagespeedonline/v5/runPagespeed*' => Http::response($this->pageSpeedFixture()),
            'https://validator.w3.org/nu/*' => Http::response([
                'messages' => [[
                    'type' => 'error',
                    'message' => 'An img element must have an alt attribute.',
                    'lastLine' => 18,
                ]],
            ]),
            'https://observatory-api.mdn.mozilla.net/api/v2/scan*' => Http::response([
                'score' => 68,
                'grade' => 'C+',
                'tests_passed' => 7,
                'tests_failed' => 3,
            ]),
        ]);

        $user = User::factory()->create(['email_verified_at' => now()]);
        $report = WebsiteReport::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $user->id,
            'requested_url' => 'https://example.com/',
            'domain' => 'example.com',
            'status' => 'queued',
            'current_stage' => 'Waiting for the audit worker',
            'progress' => 2,
            'page_limit' => 1,
        ]);

        app(WebsiteAuditRunner::class)->run($report);
        $report->refresh();

        $this->assertSame('completed', $report->status);
        $this->assertSame(100, $report->progress);
        $this->assertIsInt(data_get($report->scores, 'overall'));
        $this->assertNotEmpty($report->top_recommendations);
        $this->assertDatabaseHas('website_report_pages', [
            'website_report_id' => $report->id,
            'http_status' => 200,
        ]);
        $this->assertDatabaseHas('website_report_findings', [
            'website_report_id' => $report->id,
            'source' => 'W3C HTML Checker',
        ]);
        $this->assertDatabaseHas('website_audit_api_runs', [
            'website_report_id' => $report->id,
            'provider' => 'Google PageSpeed Insights',
            'status' => 'completed',
        ]);
        $this->assertGreaterThan(5, $report->findings()->count());

        Storage::disk('local')->assertExists($report->pdf_path);
        Storage::disk('local')->assertExists($report->data_path);
        $this->assertStringStartsWith('%PDF', Storage::disk('local')->get($report->pdf_path));

        $this->actingAs($user)
            ->get(route('reports.show', $report))
            ->assertOk()
            ->assertSee('Priority roadmap')
            ->assertSee('Detailed findings');

        $this->actingAs($user)
            ->get(route('reports.download', $report))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    /**
     * @return array<string, mixed>
     */
    private function pageSpeedFixture(): array
    {
        $auditRefs = [
            ['id' => 'largest-contentful-paint'],
            ['id' => 'unused-javascript'],
        ];

        return [
            'lighthouseResult' => [
                'lighthouseVersion' => '12.8.2',
                'fetchTime' => now()->toIso8601String(),
                'finalUrl' => 'https://example.com/',
                'categories' => [
                    'performance' => ['score' => .72, 'auditRefs' => $auditRefs],
                    'accessibility' => ['score' => .86, 'auditRefs' => [['id' => 'image-alt']]],
                    'best-practices' => ['score' => .92, 'auditRefs' => []],
                    'seo' => ['score' => .88, 'auditRefs' => []],
                ],
                'audits' => [
                    'first-contentful-paint' => [
                        'score' => .78,
                        'scoreDisplayMode' => 'numeric',
                        'title' => 'First Contentful Paint',
                        'description' => 'Time until the first content is painted.',
                        'displayValue' => '2.1 s',
                        'numericValue' => 2100,
                        'numericUnit' => 'millisecond',
                    ],
                    'largest-contentful-paint' => [
                        'score' => .42,
                        'scoreDisplayMode' => 'numeric',
                        'title' => 'Largest Contentful Paint',
                        'description' => 'Time until the largest content element is painted.',
                        'displayValue' => '4.0 s',
                        'numericValue' => 4000,
                        'numericUnit' => 'millisecond',
                    ],
                    'total-blocking-time' => [
                        'score' => .55,
                        'scoreDisplayMode' => 'numeric',
                        'title' => 'Total Blocking Time',
                        'description' => 'Long JavaScript tasks delayed interaction.',
                        'displayValue' => '460 ms',
                        'numericValue' => 460,
                        'numericUnit' => 'millisecond',
                    ],
                    'cumulative-layout-shift' => [
                        'score' => .9,
                        'scoreDisplayMode' => 'numeric',
                        'title' => 'Cumulative Layout Shift',
                        'description' => 'Visual stability during load.',
                        'displayValue' => '0.08',
                        'numericValue' => .08,
                        'numericUnit' => 'unitless',
                    ],
                    'unused-javascript' => [
                        'score' => .25,
                        'scoreDisplayMode' => 'numeric',
                        'title' => 'Reduce unused JavaScript',
                        'description' => 'Remove JavaScript that is not needed for initial rendering.',
                        'displayValue' => 'Potential savings of 180 KiB',
                    ],
                    'image-alt' => [
                        'score' => .5,
                        'scoreDisplayMode' => 'binary',
                        'title' => 'Image elements need alternative text',
                        'description' => 'Informative images should have concise alternative text.',
                    ],
                    'final-screenshot' => [
                        'score' => null,
                        'scoreDisplayMode' => 'informative',
                        'details' => [
                            'data' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9Wl2fVQAAAAASUVORK5CYII=',
                        ],
                    ],
                ],
            ],
        ];
    }

    private function auditedHtml(): string
    {
        return <<<'HTML'
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Example Growth Studio</title>
    <meta name="description" content="An example business website used to validate the detailed audit pipeline and branded report.">
    <link rel="canonical" href="https://example.com/">
    <meta property="og:title" content="Example Growth Studio">
    <style>body{font-family:sans-serif}.hero{display:grid}</style>
</head>
<body>
    <header><nav><a href="/">Home</a><a href="/contact">Contact us</a></nav></header>
    <main>
        <section class="hero">
            <h1>Grow your business with a clearer digital experience</h1>
            <p>We design conversion-focused websites and automated customer journeys.</p>
            <a href="/contact">Book a strategy call</a>
            <img src="/team.jpg">
        </section>
        <section><h2>Trusted by ambitious teams</h2><p>Our clients use our systems to improve service and sales.</p></section>
        <form><input name="email" type="email"><button>Get started</button></form>
    </main>
    <script src="/app.js"></script>
</body>
</html>
HTML;
    }
}
