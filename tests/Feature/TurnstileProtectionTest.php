<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TurnstileProtectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.turnstile.enabled' => true,
            'services.turnstile.site_key' => 'test-site-key',
            'services.turnstile.secret_key' => 'test-secret-key',
            'services.turnstile.allowed_hostname' => 'webignitors.test',
            'services.turnstile.verify_url' => 'https://challenges.cloudflare.com/turnstile/v0/siteverify',
        ]);
    }

    public function test_protected_form_displays_the_turnstile_widget(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('test-site-key')
            ->assertSee('data-action="login"', false)
            ->assertSee('https://challenges.cloudflare.com/turnstile/v0/api.js');
    }

    public function test_valid_turnstile_token_allows_a_contact_submission(): void
    {
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => true,
                'action' => 'contact',
                'hostname' => 'webignitors.test',
            ]),
        ]);

        $this->post('/contact', [
            ...$this->inquiryPayload(),
            'cf-turnstile-response' => 'valid-single-use-token',
        ])->assertRedirect(route('contact'));

        $this->assertDatabaseCount('inquiries', 1);
        Http::assertSent(fn ($request): bool => $request['secret'] === 'test-secret-key'
            && $request['response'] === 'valid-single-use-token');
    }

    public function test_missing_or_wrong_action_token_is_rejected_before_submission(): void
    {
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => true,
                'action' => 'login',
                'hostname' => 'webignitors.test',
            ]),
        ]);

        $this->from('/contact')->post('/contact', [
            ...$this->inquiryPayload(),
            'cf-turnstile-response' => 'token-for-wrong-form',
        ])
            ->assertRedirect('/contact')
            ->assertSessionHasErrors('turnstile');

        $this->assertDatabaseCount('inquiries', 0);
    }

    /** @return array<string, string> */
    private function inquiryPayload(): array
    {
        return [
            'name' => 'Prospective Client',
            'email' => 'client@example.com',
            'phone' => '',
            'company' => 'Example Company',
            'service' => 'web-development',
            'budget' => '5k-15k',
            'message' => 'We need a new customer portal and marketing website.',
            'website' => '',
        ];
    }
}
