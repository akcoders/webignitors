<?php

namespace Tests\Feature;

use App\Mail\NewInquiryNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class WebsiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_public_pages_are_available(): void
    {
        $pages = [
            '/',
            '/about',
            '/services',
            '/services/web-development',
            '/services/mobile-apps',
            '/services/digital-marketing',
            '/services/ai-integration',
            '/work',
            '/process',
            '/contact',
            '/website-audit',
        ];

        foreach ($pages as $page) {
            $this->get($page)
                ->assertOk()
                ->assertSee('WebIgnitors');
        }
    }

    public function test_a_valid_inquiry_is_stored_and_emailed(): void
    {
        Mail::fake();

        $response = $this->post('/contact', [
            'name' => 'Anuj Shukla',
            'email' => 'taylor@laravel.com',
            'phone' => '+1 555 0182',
            'company' => 'Northwind Studio',
            'service' => 'erp',
            'budget' => '15k-40k',
            'message' => 'We need a new product website and a secure customer portal this quarter.',
            'website' => '',
        ]);

        $response
            ->assertRedirect('/contact')
            ->assertSessionHas('success');

        $this->assertDatabaseHas('inquiries', [
            'name' => 'Anuj Shukla',
            'email' => 'taylor@laravel.com',
            'service' => 'erp',
            'budget' => '15k-40k',
        ]);

        Mail::assertSent(NewInquiryNotification::class, function (NewInquiryNotification $mail) {
            return $mail->inquiry->email === 'taylor@laravel.com';
        });
    }

    public function test_contact_form_requires_a_complete_valid_brief(): void
    {
        Mail::fake();

        $this->from('/contact')
            ->post('/contact', [
                'name' => 'A',
                'email' => 'not-an-email',
                'service' => 'unknown',
                'budget' => '',
                'message' => 'Too short',
            ])
            ->assertRedirect('/contact')
            ->assertSessionHasErrors(['name', 'email', 'service', 'budget', 'message']);

        $this->assertDatabaseCount('inquiries', 0);
        Mail::assertNothingSent();
    }

    public function test_honeypot_rejects_bot_submissions(): void
    {
        $this->post('/contact', [
            'name' => 'Spam Bot',
            'email' => 'taylor@laravel.com',
            'service' => 'other',
            'budget' => 'not-sure',
            'message' => 'This message is long enough but should never reach the database.',
            'website' => 'https://spam.example',
        ])->assertSessionHasErrors('website');

        $this->assertDatabaseCount('inquiries', 0);
    }
}
