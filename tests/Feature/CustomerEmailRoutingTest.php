<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CustomerEmailRoutingTest extends TestCase
{
    use RefreshDatabase;

    public function test_mail_configuration_does_not_globally_override_customer_recipients(): void
    {
        $this->assertNull(config('mail.to'));

        $user = User::factory()->create([
            'name' => 'Header Recipient',
            'email' => 'header-recipient@example.com',
        ]);
        $recipients = [];

        Event::listen(MessageSending::class, function (MessageSending $event) use (&$recipients): false {
            $recipients = array_map(
                static fn ($address): string => $address->getAddress(),
                $event->message->getTo()
            );

            return false;
        });

        $user->sendEmailVerificationNotification();

        $this->assertSame(['header-recipient@example.com'], $recipients);
    }

    public function test_registration_verification_is_addressed_to_the_registered_user(): void
    {
        Notification::fake();

        $this->post('/register', [
            'name' => 'Customer Recipient',
            'email' => 'customer-recipient@example.com',
            'password' => 'Report1234',
            'password_confirmation' => 'Report1234',
            'website' => '',
        ])->assertRedirect(route('dashboard'));

        $user = User::query()
            ->where('email', 'customer-recipient@example.com')
            ->firstOrFail();

        Notification::assertSentTo(
            $user,
            VerifyEmail::class,
            fn (VerifyEmail $notification, array $channels): bool => $channels === ['mail']
                && $user->routeNotificationForMail($notification) === [
                    'customer-recipient@example.com' => 'Customer Recipient',
                ]
        );
    }

    public function test_password_reset_is_addressed_to_the_account_email(): void
    {
        Notification::fake();
        $user = User::factory()->create([
            'name' => 'Reset Recipient',
            'email' => 'reset-recipient@example.com',
        ]);

        $this->post('/forgot-password', ['email' => $user->email])
            ->assertSessionHas('status');

        Notification::assertSentTo(
            $user,
            ResetPassword::class,
            fn (ResetPassword $notification): bool => $user->routeNotificationForMail($notification) === [
                'reset-recipient@example.com' => 'Reset Recipient',
            ]
        );
    }
}
