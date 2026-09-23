<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationRateLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_attempts_for_other_emails_do_not_block_a_valid_login_from_the_same_ip(): void
    {
        $user = User::factory()->create([
            'email' => 'owner@example.com',
            'password' => 'Report1234',
        ]);

        foreach (range(1, 5) as $attempt) {
            $this->from('/login')->post('/login', [
                'email' => "unknown{$attempt}@example.com",
                'password' => 'Incorrect1234',
            ])->assertRedirect('/login');
        }

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'Report1234',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_repeated_registration_attempts_return_a_form_error_instead_of_a_429_page(): void
    {
        foreach (range(1, 11) as $attempt) {
            $response = $this->from('/register')->post('/register', [
                'name' => '',
                'email' => 'repeated@example.com',
                'password' => 'Report1234',
                'password_confirmation' => 'Report1234',
                'website' => '',
            ]);

            $response->assertRedirect('/register');
            $this->assertNotSame(429, $response->getStatusCode());
        }

        $response->assertSessionHasErrors([
            'email' => 'Too many registration attempts. Please wait one minute and try again.',
        ]);
    }
}
